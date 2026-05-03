<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'user_kind', 'phone', 'bio', 'designation', 'aid'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable implements FilamentUser, HasMedia
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, InteractsWithMedia, Notifiable;

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if ($user->aid === null || $user->aid === '') {
                $user->aid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
        $this->addMediaCollection('photos');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 200, 200)
            ->performOnCollections('avatar', 'photos')
            ->nonQueued();
    }

    public function avatarUrl(): string
    {
        $url = $this->getFirstMediaUrl('avatar');

        if ($url !== '') {
            return $url;
        }

        $fallback = config('app.default_avatar_url', '');

        return is_string($fallback) && $fallback !== '' ? $fallback : '';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! config('artixcore.filament_panel_enabled', false)) {
            return false;
        }

        return $this->can('filament.access');
    }

    public function microToolFavorites(): BelongsToMany
    {
        return $this->belongsToMany(MicroTool::class, 'micro_tool_favorites', 'user_id', 'micro_tool_id')
            ->withTimestamps();
    }

    public function microToolRuns(): HasMany
    {
        return $this->hasMany(MicroToolRun::class);
    }

    public function microSavedReports(): HasMany
    {
        return $this->hasMany(MicroSavedReport::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function microToolHistories(): HasMany
    {
        return $this->hasMany(UserMicroToolHistory::class, 'user_id');
    }

    public function isCurrentlyPremium(): bool
    {
        return $this->subscriptions()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->where(function ($q): void {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }
}
