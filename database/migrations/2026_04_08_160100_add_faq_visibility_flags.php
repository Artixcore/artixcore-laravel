<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table): void {
            $table->boolean('show_on_general_faq')->default(true)->after('is_published');
            $table->boolean('show_on_saas_page')->default(false)->after('show_on_general_faq');
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table): void {
            $table->dropColumn(['show_on_general_faq', 'show_on_saas_page']);
        });
    }
};
