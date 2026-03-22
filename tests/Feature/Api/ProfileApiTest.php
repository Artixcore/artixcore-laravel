<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    private function portalToken(string $email = 'client@artixcore.com', string $password = 'password123'): string
    {
        $this->seed();

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);
        $login->assertOk();

        return $login->json('data.token');
    }

    public function test_profile_show_and_update(): void
    {
        Storage::fake('public');
        $token = $this->portalToken();

        $this->getJson('/api/v1/portal/profile', [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk()
            ->assertJsonPath('data.user.email', 'client@artixcore.com')
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email', 'user_kind', 'phone', 'bio', 'designation'],
                    'avatar_url',
                    'avatar_thumb_url',
                    'photos',
                ],
            ]);

        $this->patchJson('/api/v1/portal/profile', [
            'name' => 'Client Updated',
            'email' => 'client@artixcore.com',
            'phone' => '+1 555 0100',
            'bio' => 'Bio line',
            'designation' => 'PM',
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk()
            ->assertJsonPath('data.user.name', 'Client Updated')
            ->assertJsonPath('data.user.phone', '+1 555 0100')
            ->assertJsonPath('data.user.bio', 'Bio line')
            ->assertJsonPath('data.user.designation', 'PM');
    }

    public function test_profile_update_validation_errors(): void
    {
        Storage::fake('public');
        $token = $this->portalToken();

        $this->patchJson('/api/v1/portal/profile', [
            'name' => '',
            'email' => 'not-an-email',
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertStatus(422);
    }

    public function test_password_update_requires_current_password(): void
    {
        Storage::fake('public');
        $token = $this->portalToken();

        $this->putJson('/api/v1/portal/profile/password', [
            'current_password' => 'wrong',
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertStatus(422);
    }

    public function test_password_update_succeeds(): void
    {
        Storage::fake('public');
        $token = $this->portalToken();

        $this->putJson('/api/v1/portal/profile/password', [
            'current_password' => 'password123',
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk()
            ->assertJsonPath('data.message', 'Password updated.');

        /** @var User $user */
        $user = User::query()->where('email', 'client@artixcore.com')->firstOrFail();
        $this->assertTrue(Hash::check('newpassword1', $user->password));
    }

    public function test_avatar_upload_replace_and_remove(): void
    {
        Storage::fake('public');
        $token = $this->portalToken();

        $file = UploadedFile::fake()->image('avatar.jpg', 120, 120);

        $this->post('/api/v1/portal/profile/avatar', [
            'avatar' => $file,
        ], [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->assertOk()
            ->assertJsonStructure(['data' => ['user', 'avatar_url', 'photos']]);

        /** @var User $user */
        $user = User::query()->where('email', 'client@artixcore.com')->firstOrFail();
        $this->assertCount(1, $user->getMedia('avatar'));

        $file2 = UploadedFile::fake()->image('avatar2.png', 100, 100);
        $this->post('/api/v1/portal/profile/avatar', [
            'avatar' => $file2,
        ], [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->assertOk();

        $user->refresh();
        $this->assertCount(1, $user->getMedia('avatar'));

        $this->deleteJson('/api/v1/portal/profile/avatar', [], [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk();

        $user->refresh();
        $this->assertCount(0, $user->getMedia('avatar'));
    }

    public function test_photos_upload_list_and_delete(): void
    {
        Storage::fake('public');
        $token = $this->portalToken();

        $photo = UploadedFile::fake()->image('shot.jpg', 80, 80);

        $store = $this->post('/api/v1/portal/profile/photos', [
            'photo' => $photo,
        ], [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ]);
        $store->assertOk()->assertJsonStructure(['data' => ['photo' => ['id', 'url', 'thumb_url']]]);

        $photoId = (int) $store->json('data.photo.id');

        $this->getJson('/api/v1/portal/profile/photos', [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk()
            ->assertJsonPath('data.photos.0.id', $photoId);

        $this->deleteJson("/api/v1/portal/profile/photos/{$photoId}", [], [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk()
            ->assertJsonPath('data.message', 'Photo removed.');

        $this->getJson('/api/v1/portal/profile/photos', [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk()
            ->assertJsonPath('data.photos', []);
    }

    public function test_cannot_delete_another_users_photo(): void
    {
        Storage::fake('public');
        $this->seed();

        /** @var User $writer */
        $writer = User::query()->where('email', 'writer@artixcore.com')->firstOrFail();
        /** @var User $client */
        $client = User::query()->where('email', 'client@artixcore.com')->firstOrFail();

        Sanctum::actingAs($writer);

        $photo = UploadedFile::fake()->image('writer.jpg', 60, 60);
        $store = $this->post('/api/v1/portal/profile/photos', [
            'photo' => $photo,
        ], [
            'Accept' => 'application/json',
        ]);
        $store->assertOk();
        $photoId = (int) $store->json('data.photo.id');

        $this->assertDatabaseHas('media', [
            'id' => $photoId,
            'model_id' => $writer->id,
            'collection_name' => 'photos',
        ]);

        Sanctum::actingAs($client);

        $this->deleteJson("/api/v1/portal/profile/photos/{$photoId}")->assertNotFound();
    }
}
