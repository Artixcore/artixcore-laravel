<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotent repair: adds website-capture columns only if missing.
 * Safe when a prior migration was skipped or partially applied. No data loss.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('leads')) {
            return;
        }

        if (! Schema::hasColumn('leads', 'service_type')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->string('service_type', 120)->nullable();
            });
        }

        if (! Schema::hasColumn('leads', 'message')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->text('message')->nullable();
            });
        }

        if (! Schema::hasColumn('leads', 'ip_address')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->string('ip_address', 45)->nullable();
            });
        }

        if (! Schema::hasColumn('leads', 'user_agent')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->text('user_agent')->nullable();
            });
        }

        if (! Schema::hasColumn('leads', 'submitted_at')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->timestamp('submitted_at')->nullable();
            });
        }

        if (! Schema::hasColumn('leads', 'reviewed_at')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->timestamp('reviewed_at')->nullable();
            });
        }

        if (! Schema::hasColumn('leads', 'admin_notes')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->text('admin_notes')->nullable();
            });
        }

        if (! Schema::hasColumn('leads', 'deleted_at')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        if (! Schema::hasColumn('leads', 'reviewed_by')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Non-destructive repair migration: no down that drops columns.
    }
};
