<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_sections', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('secondary_button_text')->nullable();
            $table->string('secondary_button_url')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('homepage_section_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('homepage_section_id')
                ->constrained('homepage_sections')
                ->cascadeOnDelete();
            $table->string('item_type', 100)->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('title_override')->nullable();
            $table->text('description_override')->nullable();
            $table->string('image_override')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['homepage_section_id', 'sort_order']);
            $table->index(['item_type', 'item_id']);
        });

        Schema::table('site_settings', function (Blueprint $table): void {
            $table->json('homepage_seo')->nullable()->after('homepage_content');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn('homepage_seo');
        });

        Schema::dropIfExists('homepage_section_items');
        Schema::dropIfExists('homepage_sections');
    }
};
