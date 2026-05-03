<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_generation_logs', function (Blueprint $table): void {
            $table->id();
            $table->date('log_date')->index();
            $table->string('status', 32)->default('success');
            $table->string('article_type')->nullable();
            $table->string('error_message')->nullable();
            $table->unsignedInteger('articles_created')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_generation_logs');
    }
};
