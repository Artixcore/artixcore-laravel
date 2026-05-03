<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terms', function (Blueprint $table): void {
            $table->foreignId('parent_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('terms', function (Blueprint $table): void {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropColumn(['parent_id', 'meta_title', 'meta_description']);
        });
    }
};
