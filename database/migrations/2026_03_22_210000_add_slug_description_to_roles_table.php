<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('guard_name');
            $table->text('description')->nullable()->after('slug');
            $table->unique(['guard_name', 'slug'], 'roles_guard_name_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->dropUnique('roles_guard_name_slug_unique');
            $table->dropColumn(['slug', 'description']);
        });
    }
};
