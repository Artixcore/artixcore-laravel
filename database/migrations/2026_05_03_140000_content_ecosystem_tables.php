<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_relations')) {
            Schema::create('content_relations', function (Blueprint $table): void {
                $table->id();
                $table->string('source_type', 100);
                $table->unsignedBigInteger('source_id');
                $table->string('related_type', 100);
                $table->unsignedBigInteger('related_id');
                $table->string('relation_type', 80)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_featured')->default(false);
                $table->timestamps();

                $table->index(['source_type', 'source_id'], 'content_relations_source_index');
                $table->index(['related_type', 'related_id'], 'content_relations_related_index');
                $table->index('relation_type', 'content_relations_relation_type_index');
                $table->unique(
                    ['source_type', 'source_id', 'related_type', 'related_id', 'relation_type'],
                    'content_relations_unique_link'
                );
            });
        }

        if (! Schema::hasTable('faqables')) {
            Schema::create('faqables', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('faq_id')->constrained('faqs')->cascadeOnDelete();
                $table->string('faqable_type', 100);
                $table->unsignedBigInteger('faqable_id');
                $table->index(['faqable_type', 'faqable_id'], 'faqables_faqable_type_faqable_id_index');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['faq_id', 'faqable_type', 'faqable_id'], 'faqables_unique');
            });
        }

        if (! Schema::hasTable('testimonialables')) {
            Schema::create('testimonialables', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('testimonial_id')->constrained('testimonials')->cascadeOnDelete();
                $table->string('testimonialable_type', 100);
                $table->unsignedBigInteger('testimonialable_id');
                $table->index(['testimonialable_type', 'testimonialable_id'], 'testimonialables_testimonialable_type_testimonialable_id_index');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['testimonial_id', 'testimonialable_type', 'testimonialable_id'], 'testimonialables_unique');
            });
        }

        if (! Schema::hasTable('portfolio_items')) {
            Schema::create('portfolio_items', function (Blueprint $table): void {
                $table->id();
                $table->string('slug', 191)->unique();
                $table->string('title');
                $table->string('client_name')->nullable();
                $table->string('project_type')->nullable();
                $table->string('industry')->nullable();
                $table->string('short_description')->nullable();
                $table->longText('body')->nullable();
                $table->longText('challenge')->nullable();
                $table->longText('solution')->nullable();
                $table->json('technology_stack')->nullable();
                $table->longText('outcome')->nullable();
                $table->foreignId('main_image_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
                $table->json('gallery_media_ids')->nullable();
                $table->string('video_url')->nullable();
                $table->string('video_provider')->nullable();
                $table->string('status')->default('draft');
                $table->boolean('featured')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->string('canonical_url')->nullable();
                $table->string('robots')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::table('services', function (Blueprint $table): void {
            if (! Schema::hasColumn('services', 'featured')) {
                $table->boolean('featured')->default(false)->after('sort_order');
            }
            if (! Schema::hasColumn('services', 'benefits')) {
                $table->json('benefits')->nullable()->after('body');
            }
            if (! Schema::hasColumn('services', 'process')) {
                $table->json('process')->nullable()->after('benefits');
            }
            if (! Schema::hasColumn('services', 'technologies')) {
                $table->json('technologies')->nullable()->after('process');
            }
            if (! Schema::hasColumn('services', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('meta');
            }
            if (! Schema::hasColumn('services', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (! Schema::hasColumn('services', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('services', 'canonical_url')) {
                $table->string('canonical_url')->nullable()->after('meta_keywords');
            }
            if (! Schema::hasColumn('services', 'robots')) {
                $table->string('robots')->nullable()->after('canonical_url');
            }
        });

        Schema::table('products', function (Blueprint $table): void {
            if (! Schema::hasColumn('products', 'platform_type')) {
                $table->string('platform_type')->nullable()->after('tagline');
            }
            if (! Schema::hasColumn('products', 'features')) {
                $table->json('features')->nullable()->after('platform_type');
            }
            if (! Schema::hasColumn('products', 'target_audience')) {
                $table->text('target_audience')->nullable()->after('features');
            }
            if (! Schema::hasColumn('products', 'pricing_note')) {
                $table->text('pricing_note')->nullable()->after('target_audience');
            }
            if (! Schema::hasColumn('products', 'use_cases')) {
                $table->json('use_cases')->nullable()->after('pricing_note');
            }
            if (! Schema::hasColumn('products', 'video_url')) {
                $table->string('video_url')->nullable()->after('body');
            }
            if (! Schema::hasColumn('products', 'video_provider')) {
                $table->string('video_provider')->nullable()->after('video_url');
            }
            if (! Schema::hasColumn('products', 'main_image_media_id')) {
                $table->foreignId('main_image_media_id')->nullable()->after('video_provider')->constrained('media_assets')->nullOnDelete();
            }
            if (! Schema::hasColumn('products', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('products', 'canonical_url')) {
                $table->string('canonical_url')->nullable()->after('meta_keywords');
            }
            if (! Schema::hasColumn('products', 'robots')) {
                $table->string('robots')->nullable()->after('canonical_url');
            }
        });

        Schema::table('faqs', function (Blueprint $table): void {
            if (! Schema::hasColumn('faqs', 'category')) {
                $table->string('category')->nullable()->after('answer');
            }
        });

        Schema::table('testimonials', function (Blueprint $table): void {
            if (! Schema::hasColumn('testimonials', 'rating')) {
                $table->unsignedTinyInteger('rating')->nullable()->after('body');
            }
            if (! Schema::hasColumn('testimonials', 'featured')) {
                $table->boolean('featured')->default(false)->after('is_published');
            }
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table): void {
            if (Schema::hasColumn('testimonials', 'featured')) {
                $table->dropColumn('featured');
            }
            if (Schema::hasColumn('testimonials', 'rating')) {
                $table->dropColumn('rating');
            }
        });

        Schema::table('faqs', function (Blueprint $table): void {
            if (Schema::hasColumn('faqs', 'category')) {
                $table->dropColumn('category');
            }
        });

        Schema::table('products', function (Blueprint $table): void {
            foreach ([
                'main_image_media_id',
                'video_provider',
                'video_url',
                'use_cases',
                'pricing_note',
                'target_audience',
                'features',
                'platform_type',
                'robots',
                'canonical_url',
                'meta_keywords',
            ] as $col) {
                if (Schema::hasColumn('products', $col)) {
                    if ($col === 'main_image_media_id') {
                        $table->dropForeign(['main_image_media_id']);
                    }
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('services', function (Blueprint $table): void {
            foreach ([
                'robots',
                'canonical_url',
                'meta_keywords',
                'meta_description',
                'meta_title',
                'technologies',
                'process',
                'benefits',
                'featured',
            ] as $col) {
                if (Schema::hasColumn('services', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::dropIfExists('portfolio_items');
        Schema::dropIfExists('testimonialables');
        Schema::dropIfExists('faqables');
        Schema::dropIfExists('content_relations');
    }
};
