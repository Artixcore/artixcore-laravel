<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_updates', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('market_area')->nullable();
            $table->longText('trend_summary')->nullable();
            $table->longText('business_impact')->nullable();
            $table->longText('technology_impact')->nullable();
            $table->longText('opportunities')->nullable();
            $table->longText('risks')->nullable();
            $table->longText('what_next')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots')->default('index,follow');
            $table->string('main_image_path')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_provider')->nullable();
            $table->unsignedInteger('reading_time_minutes')->nullable();
            $table->text('fact_check_notes')->nullable();
            $table->text('source_requirements')->nullable();
            $table->json('source_urls')->nullable();
            $table->string('status')->default('draft');
            $table->string('source_topic')->nullable();
            $table->longText('ai_prompt')->nullable();
            $table->json('ai_generation_meta')->nullable();
            $table->string('author_name')->default('Ali 1.0');
            $table->string('author_type', 32)->default('ai');
            $table->string('ai_model')->nullable();
            $table->boolean('slug_locked')->default(false);
            $table->boolean('review_required')->default(true);
            $table->boolean('featured')->default(false);
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_updates');
    }
};
