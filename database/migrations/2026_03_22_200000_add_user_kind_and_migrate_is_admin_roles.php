<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'user_kind')) {
                $table->string('user_kind', 32)->default('internal')->after('password');
            }
        });

        if (! Schema::hasColumn('users', 'is_admin')) {
            return;
        }

        $role = Role::findOrCreate('master_admin', 'web');

        foreach (User::query()->where('is_admin', true)->cursor() as $user) {
            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('password');
            }
        });

        $role = Role::query()->where('name', 'master_admin')->where('guard_name', 'web')->first();
        if ($role !== null) {
            foreach ($role->users()->cursor() as $user) {
                $user->forceFill(['is_admin' => true])->saveQuietly();
            }
        }

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'user_kind')) {
                $table->dropColumn('user_kind');
            }
        });
    }
};
