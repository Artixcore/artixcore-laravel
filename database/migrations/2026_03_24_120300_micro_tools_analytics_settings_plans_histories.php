<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_micro_tool_usage', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('micro_tool_id')->constrained('micro_tools')->cascadeOnDelete();
            $table->string('guest_token', 128)->nullable()->index();
            $table->string('session_id', 128)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->date('usage_date');
            $table->unsignedInteger('total_runs')->default(0);
            $table->unsignedInteger('ads_shown_count')->default(0);
            $table->timestamps();

            $table->unique(['micro_tool_id', 'guest_token', 'usage_date'], 'guest_micro_tool_usage_tool_token_date');
        });

        Schema::create('micro_tool_usage_daily_stats', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('micro_tool_id')->constrained('micro_tools')->cascadeOnDelete();
            $table->date('stat_date');
            $table->unsignedInteger('total_runs')->default(0);
            $table->unsignedInteger('guest_runs')->default(0);
            $table->unsignedInteger('free_user_runs')->default(0);
            $table->unsignedInteger('paid_user_runs')->default(0);
            $table->unsignedInteger('success_runs')->default(0);
            $table->unsignedInteger('failed_runs')->default(0);
            $table->unsignedInteger('saved_reports_count')->default(0);
            $table->unsignedInteger('ads_views_count')->default(0);
            $table->unsignedInteger('unique_users_count')->default(0);
            $table->unsignedInteger('unique_guests_count')->default(0);
            $table->timestamps();

            $table->unique(['micro_tool_id', 'stat_date']);
        });

        Schema::create('micro_tool_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('micro_tool_id')->constrained('micro_tools')->cascadeOnDelete();
            $table->string('key', 128);
            $table->longText('value')->nullable();
            $table->string('type', 32)->nullable();
            $table->timestamps();

            $table->unique(['micro_tool_id', 'key']);
        });

        Schema::create('micro_tool_status_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('micro_tool_id')->constrained('micro_tools')->cascadeOnDelete();
            $table->string('action', 64);
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('micro_tool_access_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('micro_tool_id')->constrained('micro_tools')->cascadeOnDelete();
            $table->string('plan_type', 32);
            $table->unsignedInteger('usage_limit_daily')->nullable();
            $table->unsignedInteger('usage_limit_monthly')->nullable();
            $table->boolean('ads_enabled')->default(true);
            $table->boolean('export_enabled')->default(true);
            $table->boolean('saved_history_enabled')->default(true);
            $table->boolean('priority_queue_enabled')->default(false);
            $table->timestamps();

            $table->unique(['micro_tool_id', 'plan_type']);
        });

        Schema::table('micro_saved_reports', function (Blueprint $table): void {
            $table->json('report_data')->nullable()->after('title');
            $table->string('report_format', 32)->nullable()->after('report_data');
            $table->string('visibility', 32)->nullable()->after('report_format');
        });

        Schema::create('user_micro_tool_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('micro_tool_id')->constrained('micro_tools')->cascadeOnDelete();
            $table->foreignId('micro_tool_run_id')->constrained('micro_tool_runs')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('summary')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_saved')->default(true);
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'micro_tool_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_micro_tool_histories');

        Schema::table('micro_saved_reports', function (Blueprint $table): void {
            $table->dropColumn(['report_data', 'report_format', 'visibility']);
        });

        Schema::dropIfExists('micro_tool_access_plans');
        Schema::dropIfExists('micro_tool_status_logs');
        Schema::dropIfExists('micro_tool_settings');
        Schema::dropIfExists('micro_tool_usage_daily_stats');
        Schema::dropIfExists('guest_micro_tool_usage');
    }
};
