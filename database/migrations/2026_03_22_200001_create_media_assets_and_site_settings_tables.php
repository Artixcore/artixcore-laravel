<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table): void {
            $table->id();
            $table->string('disk')->default('public');
            $table->string('directory')->default('media');
            $table->string('path');
            $table->string('filename');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('logo_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->foreignId('favicon_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->foreignId('og_default_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->string('site_name')->nullable();
            $table->string('default_meta_title')->nullable();
            $table->text('default_meta_description')->nullable();
            $table->string('contact_email')->nullable();
            $table->json('social_links')->nullable();
            $table->json('design_tokens')->nullable();
            $table->string('theme_default')->default('system');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('media_assets');
    }
};
