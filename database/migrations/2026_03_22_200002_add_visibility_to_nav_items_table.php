<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nav_items', function (Blueprint $table): void {
            $table->json('visibility')->nullable()->after('feature_payload');
        });
    }

    public function down(): void
    {
        Schema::table('nav_items', function (Blueprint $table): void {
            $table->dropColumn('visibility');
        });
    }
};
