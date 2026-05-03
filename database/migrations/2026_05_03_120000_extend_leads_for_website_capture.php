<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->string('service_type', 120)->nullable();
            $table->text('message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->softDeletes();

            $table->index('service_type');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropSoftDeletes();
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropIndex(['service_type']);
            $table->dropColumn([
                'service_type',
                'message',
                'ip_address',
                'user_agent',
                'submitted_at',
                'reviewed_at',
                'admin_notes',
            ]);
        });
    }
};
