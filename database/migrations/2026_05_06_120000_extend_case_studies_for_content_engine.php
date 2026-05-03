<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_studies', function (Blueprint $table): void {
            $table->string('case_study_type', 32)->default('concept');
            $table->boolean('client_verified')->default(false);
            $table->string('client_display_name')->nullable();
            $table->string('industry')->nullable();
            $table->string('project_type')->nullable();
            $table->longText('challenge')->nullable();
            $table->longText('solution')->nullable();
            $table->longText('implementation')->nullable();
            $table->json('technology_stack')->nullable();
            $table->json('outcomes')->nullable();
            $table->json('metrics')->nullable();
            $table->longText('lessons_learned')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots')->default('index,follow');
            $table->string('main_image_path')->nullable();
            $table->json('gallery_paths')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_provider')->nullable();
            $table->unsignedInteger('reading_time_minutes')->nullable();
            $table->text('originality_notes')->nullable();
            $table->text('fact_check_notes')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->string('source_topic')->nullable();
            $table->longText('ai_prompt')->nullable();
            $table->json('ai_generation_meta')->nullable();
            $table->string('author_name')->default('Ali 1.0');
            $table->string('author_type', 32)->default('ai');
            $table->string('ai_model')->nullable();
            $table->boolean('slug_locked')->default(false);
            $table->boolean('review_required')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('case_studies', function (Blueprint $table): void {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'case_study_type',
                'client_verified',
                'client_display_name',
                'industry',
                'project_type',
                'challenge',
                'solution',
                'implementation',
                'technology_stack',
                'outcomes',
                'metrics',
                'lessons_learned',
                'meta_keywords',
                'canonical_url',
                'robots',
                'main_image_path',
                'gallery_paths',
                'video_url',
                'video_provider',
                'reading_time_minutes',
                'originality_notes',
                'fact_check_notes',
                'scheduled_for',
                'source_topic',
                'ai_prompt',
                'ai_generation_meta',
                'author_name',
                'author_type',
                'ai_model',
                'slug_locked',
                'review_required',
                'created_by',
                'updated_by',
            ]);
        });
    }
};
