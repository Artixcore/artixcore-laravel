<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('micro_tool_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('micro_tool_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('micro_tools', function (Blueprint $table): void {
            $table->foreignId('micro_tool_category_id')->nullable()->after('id')->constrained('micro_tool_categories')->nullOnDelete();
            $table->string('short_description')->nullable()->after('description');
            $table->string('route_path')->nullable()->after('short_description');
            $table->string('tool_type', 64)->nullable()->after('route_path');
            $table->string('input_type', 64)->nullable()->after('tool_type');
            $table->string('output_type', 64)->nullable()->after('input_type');
            $table->string('access_type', 32)->default('public')->after('output_type');
            $table->boolean('is_public')->default(true)->after('access_type');
            $table->boolean('requires_auth')->default(false)->after('is_public');
            $table->boolean('ads_enabled')->default(true)->after('requires_auth');
            $table->boolean('is_featured')->default(false)->after('ads_enabled');
            $table->string('version', 32)->nullable()->after('is_featured');
            $table->foreignId('created_by')->nullable()->after('version')->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });

        if (Schema::hasTable('micro_tools')) {
            $distinct = DB::table('micro_tools')->select('category')->distinct()->pluck('category')->filter();
            $sort = 0;
            foreach ($distinct as $cat) {
                if ($cat === null || $cat === '') {
                    continue;
                }
                $slug = Str::slug((string) $cat);
                if ($slug === '') {
                    $slug = 'category-'.(++$sort);
                }
                $exists = DB::table('micro_tool_categories')->where('slug', $slug)->exists();
                if (! $exists) {
                    DB::table('micro_tool_categories')->insert([
                        'parent_id' => null,
                        'name' => Str::title(str_replace(['-', '_'], ' ', (string) $cat)),
                        'slug' => $slug,
                        'description' => null,
                        'icon' => null,
                        'sort_order' => ++$sort,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $categories = DB::table('micro_tool_categories')->pluck('id', 'slug');
            foreach (DB::table('micro_tools')->select('id', 'category')->cursor() as $row) {
                if ($row->category === null || $row->category === '') {
                    continue;
                }
                $slug = Str::slug((string) $row->category);
                $cid = $categories[$slug] ?? null;
                if ($cid !== null) {
                    DB::table('micro_tools')->where('id', $row->id)->update(['micro_tool_category_id' => $cid]);
                }
            }

            foreach (DB::table('micro_tools')->cursor() as $tool) {
                $access = $tool->is_premium ? 'premium' : 'public';
                DB::table('micro_tools')->where('id', $tool->id)->update([
                    'access_type' => $access,
                    'is_public' => true,
                    'requires_auth' => false,
                    'ads_enabled' => true,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('micro_tools', function (Blueprint $table): void {
            $table->dropForeign(['micro_tool_category_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn([
                'micro_tool_category_id',
                'short_description',
                'route_path',
                'tool_type',
                'input_type',
                'output_type',
                'access_type',
                'is_public',
                'requires_auth',
                'ads_enabled',
                'is_featured',
                'version',
                'created_by',
                'updated_by',
            ]);
        });

        Schema::dropIfExists('micro_tool_categories');
    }
};
