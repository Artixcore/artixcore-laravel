<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('article_generation_logs', function (Blueprint $table): void {
            $table->string('content_type', 64)->nullable()->after('article_type');
            $table->unsignedInteger('records_created')->default(0)->after('articles_created');
            $table->string('payload_summary')->nullable()->after('error_message');
        });
    }

    public function down(): void
    {
        Schema::table('article_generation_logs', function (Blueprint $table): void {
            $table->dropColumn(['content_type', 'records_created', 'payload_summary']);
        });
    }
};
