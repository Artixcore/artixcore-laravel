<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->timestamp('scheduled_for')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots')->default('index,follow');
            $table->string('main_image_path')->nullable();
            $table->string('author_name')->default('Ali 1.0');
            $table->string('author_type')->default('ai');
            $table->string('ai_model')->nullable();
            $table->longText('ai_prompt')->nullable();
            $table->json('ai_generation_meta')->nullable();
            $table->string('source_topic')->nullable();
            $table->string('article_type')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_provider')->nullable();
            $table->unsignedInteger('reading_time_minutes')->nullable();
            $table->decimal('plagiarism_score', 5, 2)->nullable();
            $table->text('originality_notes')->nullable();
            $table->boolean('review_required')->default(false);
            $table->boolean('slug_locked')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'scheduled_for',
                'meta_keywords',
                'canonical_url',
                'robots',
                'main_image_path',
                'author_name',
                'author_type',
                'ai_model',
                'ai_prompt',
                'ai_generation_meta',
                'source_topic',
                'article_type',
                'video_url',
                'video_provider',
                'reading_time_minutes',
                'plagiarism_score',
                'originality_notes',
                'review_required',
                'slug_locked',
                'created_by',
                'updated_by',
            ]);
        });
    }
};
