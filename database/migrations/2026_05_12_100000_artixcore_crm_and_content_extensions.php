<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('crm_sources')) {
            Schema::create('crm_sources', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('crm_projects')) {
            Schema::create('crm_projects', function (Blueprint $table): void {
                $table->id();
                $table->string('title');
                $table->string('slug')->nullable()->unique();
                $table->string('status', 32)->default('planning');
                $table->string('service_type')->nullable();
                $table->decimal('budget_amount', 15, 2)->nullable();
                $table->string('currency', 8)->default('USD');
                $table->date('start_date')->nullable();
                $table->date('due_date')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->text('description')->nullable();
                $table->text('internal_notes')->nullable();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('crm_contacts')) {
            Schema::create('crm_contacts', function (Blueprint $table): void {
                $table->id();
                $table->string('type', 32)->default('lead');
                $table->string('status', 40)->default('new');
                $table->string('name');
                $table->string('email')->nullable()->index();
                $table->string('phone')->nullable();
                $table->string('company_name')->nullable();
                $table->string('job_title')->nullable();
                $table->string('website')->nullable();
                $table->foreignId('source_id')->nullable()->constrained('crm_sources')->nullOnDelete();
                $table->string('source_detail')->nullable();
                $table->string('service_interest')->nullable()->index();
                $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
                $table->foreignId('saas_platform_id')->nullable()->constrained('products')->nullOnDelete();
                $table->foreignId('project_id')->nullable()->constrained('crm_projects')->nullOnDelete();
                $table->string('industry')->nullable()->index();
                $table->string('company_size')->nullable();
                $table->string('budget_range')->nullable();
                $table->string('priority', 16)->default('normal');
                $table->string('geo_country')->nullable();
                $table->string('geo_region')->nullable();
                $table->string('geo_city')->nullable();
                $table->string('geo_postal')->nullable();
                $table->decimal('geo_latitude', 10, 7)->nullable();
                $table->decimal('geo_longitude', 10, 7)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('last_contacted_at')->nullable();
                $table->timestamp('next_follow_up_at')->nullable();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('converted_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('crm_projects') && ! Schema::hasColumn('crm_projects', 'contact_id')) {
            Schema::table('crm_projects', function (Blueprint $table): void {
                $table->foreignId('contact_id')->nullable()->after('id')->constrained('crm_contacts')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('crm_contact_notes')) {
            Schema::create('crm_contact_notes', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('contact_id')->constrained('crm_contacts')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('type', 32)->default('note');
                $table->string('title')->nullable();
                $table->text('body');
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('crm_email_templates')) {
            Schema::create('crm_email_templates', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('subject');
                $table->text('body');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::table('testimonials', function (Blueprint $table): void {
            if (! Schema::hasColumn('testimonials', 'moderation_status')) {
                $table->string('moderation_status', 32)->default('approved')->after('is_published');
            }
            if (! Schema::hasColumn('testimonials', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('moderation_status');
            }
            if (! Schema::hasColumn('testimonials', 'submitter_email')) {
                $table->string('submitter_email')->nullable()->after('company');
            }
            if (! Schema::hasColumn('testimonials', 'company_logo_media_id')) {
                $table->foreignId('company_logo_media_id')->nullable()->after('avatar_media_id')->constrained('media_assets')->nullOnDelete();
            }
            if (! Schema::hasColumn('testimonials', 'service_id')) {
                $table->foreignId('service_id')->nullable()->after('company_logo_media_id')->constrained('services')->nullOnDelete();
            }
            if (! Schema::hasColumn('testimonials', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('service_id')->constrained('products')->nullOnDelete();
            }
            if (! Schema::hasColumn('testimonials', 'portfolio_item_id')) {
                $table->foreignId('portfolio_item_id')->nullable()->after('product_id')->constrained('portfolio_items')->nullOnDelete();
            }
            if (! Schema::hasColumn('testimonials', 'case_study_id')) {
                $table->foreignId('case_study_id')->nullable()->after('portfolio_item_id')->constrained('case_studies')->nullOnDelete();
            }
            if (! Schema::hasColumn('testimonials', 'crm_contact_id')) {
                $table->foreignId('crm_contact_id')->nullable()->after('case_study_id')->constrained('crm_contacts')->nullOnDelete();
            }
            if (! Schema::hasColumn('testimonials', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('featured')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('testimonials', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('testimonials', 'moderation_status')) {
            DB::table('testimonials')->where('is_published', true)->update([
                'moderation_status' => 'approved',
                'published_at' => DB::raw('COALESCE(published_at, updated_at, created_at)'),
            ]);
            DB::table('testimonials')->where('is_published', false)->update([
                'moderation_status' => 'pending',
            ]);
        }

        Schema::table('faqs', function (Blueprint $table): void {
            if (! Schema::hasColumn('faqs', 'status')) {
                $table->string('status', 32)->default('published')->after('is_published');
            }
            if (! Schema::hasColumn('faqs', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('sort_order');
            }
            if (! Schema::hasColumn('faqs', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('is_featured');
            }
            if (! Schema::hasColumn('faqs', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (! Schema::hasColumn('faqs', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('meta_description')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('faqs', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
        });

        if (Schema::hasTable('faqs') && ! Schema::hasColumn('faqs', 'deleted_at')) {
            Schema::table('faqs', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        if (Schema::hasColumn('faqs', 'status')) {
            DB::table('faqs')->where('is_published', true)->update(['status' => 'published']);
            DB::table('faqs')->where('is_published', false)->update(['status' => 'draft']);
        }

        if (! Schema::hasColumn('faqs', 'seed_key')) {
            Schema::table('faqs', function (Blueprint $table): void {
                $table->string('seed_key')->nullable()->unique()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('faqs', 'seed_key')) {
            Schema::table('faqs', function (Blueprint $table): void {
                $table->dropUnique(['seed_key']);
                $table->dropColumn('seed_key');
            });
        }

        if (Schema::hasColumn('faqs', 'deleted_at')) {
            Schema::table('faqs', function (Blueprint $table): void {
                $table->dropSoftDeletes();
            });
        }

        Schema::table('faqs', function (Blueprint $table): void {
            if (Schema::hasColumn('faqs', 'updated_by')) {
                $table->dropConstrainedForeignId('updated_by');
            }
            if (Schema::hasColumn('faqs', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
            foreach (['meta_description', 'meta_title', 'is_featured', 'status'] as $col) {
                if (Schema::hasColumn('faqs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table): void {
                foreach (['updated_by', 'created_by', 'crm_contact_id', 'case_study_id', 'portfolio_item_id', 'product_id', 'service_id', 'company_logo_media_id'] as $col) {
                    if (Schema::hasColumn('testimonials', $col)) {
                        $table->dropConstrainedForeignId($col);
                    }
                }
                foreach (['submitter_email', 'published_at', 'moderation_status'] as $col) {
                    if (Schema::hasColumn('testimonials', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        Schema::dropIfExists('crm_email_templates');
        Schema::dropIfExists('crm_contact_notes');

        if (Schema::hasTable('crm_projects') && Schema::hasColumn('crm_projects', 'contact_id')) {
            Schema::table('crm_projects', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('contact_id');
            });
        }

        Schema::dropIfExists('crm_contacts');
        Schema::dropIfExists('crm_projects');
        Schema::dropIfExists('crm_sources');
    }
};
