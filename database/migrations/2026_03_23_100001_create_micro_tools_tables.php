<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('micro_tools', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('category', 64);
            $table->string('title');
            $table->text('description');
            $table->string('icon_key')->nullable();
            $table->string('execution_mode', 16);
            $table->json('limits')->nullable();
            $table->json('input_schema')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_premium')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('released_at')->nullable();
            $table->unsignedInteger('featured_score')->default(0);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_new')->default(false);
            $table->timestamps();
        });

        Schema::create('micro_tool_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('micro_tool_id')->constrained('micro_tools')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_key', 64)->nullable()->index();
            $table->json('input_summary')->nullable();
            $table->string('status', 32)->default('pending');
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('error_code', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('micro_tool_run_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('micro_tool_run_id')->constrained('micro_tool_runs')->cascadeOnDelete();
            $table->json('payload');
            $table->timestamps();
        });

        Schema::create('micro_tool_favorites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('micro_tool_id')->constrained('micro_tools')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'micro_tool_id']);
        });

        Schema::create('micro_saved_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('micro_tool_id')->constrained('micro_tools')->cascadeOnDelete();
            $table->foreignId('micro_tool_run_id')->constrained('micro_tool_runs')->cascadeOnDelete();
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('micro_subscription_limits', function (Blueprint $table): void {
            $table->id();
            $table->string('tier')->unique();
            $table->json('limits');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('micro_saved_reports');
        Schema::dropIfExists('micro_tool_favorites');
        Schema::dropIfExists('micro_tool_run_results');
        Schema::dropIfExists('micro_tool_runs');
        Schema::dropIfExists('micro_tools');
        Schema::dropIfExists('micro_subscription_limits');
    }
};
