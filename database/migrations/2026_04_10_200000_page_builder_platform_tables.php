<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->timestamp('archived_at')->nullable()->after('published_at');
            $table->timestamp('scheduled_publish_at')->nullable()->after('archived_at');
            $table->foreignId('meta_og_media_id')->nullable()->after('meta_description')->constrained('media_assets')->nullOnDelete();
            $table->text('custom_head_html')->nullable()->after('meta');
            $table->text('custom_body_html')->nullable()->after('custom_head_html');
            $table->json('builder_settings_json')->nullable()->after('custom_body_html');
        });

        Schema::create('page_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('label', 32)->default('autosave');
            $table->json('document_json');
            $table->timestamps();

            $table->index(['page_id', 'id']);
            $table->index(['page_id', 'created_at']);
        });

        Schema::create('builder_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category', 64)->nullable();
            $table->string('description')->nullable();
            $table->json('document_json');
            $table->foreignId('thumbnail_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'category']);
        });

        Schema::create('builder_saved_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->json('document_json');
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::create('ai_builder_business_profiles', function (Blueprint $table): void {
            $table->id();
            $table->string('business_name')->nullable();
            $table->text('brand_summary')->nullable();
            $table->string('business_type', 64)->nullable();
            $table->text('target_audience')->nullable();
            $table->text('main_services')->nullable();
            $table->text('unique_selling_points')->nullable();
            $table->string('tone_of_voice', 128)->nullable();
            $table->text('offer_details')->nullable();
            $table->string('location')->nullable();
            $table->json('contact_details')->nullable();
            $table->string('preferred_cta_goal', 128)->nullable();
            $table->string('writing_style', 128)->nullable();
            $table->text('forbidden_topics')->nullable();
            $table->json('brand_colors')->nullable();
            $table->text('style_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_generation_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->foreignId('page_version_id')->nullable()->constrained('page_versions')->nullOnDelete();
            $table->foreignId('ai_provider_id')->nullable()->constrained('ai_providers')->nullOnDelete();
            $table->string('action', 64);
            $table->string('request_summary', 512)->nullable();
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->string('status', 32)->default('ok');
            $table->json('metadata_json')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['page_id', 'created_at']);
        });

        Schema::table('platform_security_settings', function (Blueprint $table): void {
            $table->unsignedInteger('builder_ai_rate_limit_per_minute')->default(30)->after('chat_rate_limit_per_day');
        });
    }

    public function down(): void
    {
        Schema::table('platform_security_settings', function (Blueprint $table): void {
            $table->dropColumn('builder_ai_rate_limit_per_minute');
        });

        Schema::dropIfExists('ai_generation_logs');
        Schema::dropIfExists('ai_builder_business_profiles');
        Schema::dropIfExists('builder_saved_sections');
        Schema::dropIfExists('builder_templates');
        Schema::dropIfExists('page_versions');

        Schema::table('pages', function (Blueprint $table): void {
            $table->dropForeign(['meta_og_media_id']);
            $table->dropColumn([
                'archived_at',
                'scheduled_publish_at',
                'meta_og_media_id',
                'custom_head_html',
                'custom_body_html',
                'builder_settings_json',
            ]);
        });
    }
};
