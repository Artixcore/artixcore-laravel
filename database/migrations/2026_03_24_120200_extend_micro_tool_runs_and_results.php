<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('micro_tool_runs')->where('status', 'completed')->update(['status' => 'success']);

        Schema::table('micro_tool_runs', function (Blueprint $table): void {
            $table->string('guest_token', 128)->nullable()->after('guest_key')->index();
            $table->string('session_id', 128)->nullable()->after('guest_token')->index();
            $table->string('request_ip', 45)->nullable()->after('session_id');
            $table->string('request_hash', 128)->nullable()->after('request_ip')->index();
            $table->text('result_summary')->nullable()->after('request_hash');
            $table->boolean('is_guest')->default(true)->after('result_summary');
            $table->boolean('is_registered')->default(false)->after('is_guest');
            $table->boolean('is_aid_user')->default(false)->after('is_registered');
            $table->boolean('is_paid_user')->default(false)->after('is_aid_user');
            $table->boolean('ads_shown')->default(false)->after('is_paid_user');
            $table->boolean('is_saved')->default(false)->after('ads_shown');
            $table->string('source', 32)->nullable()->after('is_saved');
        });

        Schema::table('micro_tool_run_results', function (Blueprint $table): void {
            $table->string('result_type', 64)->nullable()->after('micro_tool_run_id');
            $table->unsignedInteger('warning_count')->default(0)->after('payload');
            $table->unsignedInteger('error_count')->default(0)->after('warning_count');
            $table->decimal('score', 8, 2)->nullable()->after('error_count');
            $table->boolean('is_exportable')->default(true)->after('score');
        });
    }

    public function down(): void
    {
        Schema::table('micro_tool_run_results', function (Blueprint $table): void {
            $table->dropColumn([
                'result_type',
                'warning_count',
                'error_count',
                'score',
                'is_exportable',
            ]);
        });

        Schema::table('micro_tool_runs', function (Blueprint $table): void {
            $table->dropColumn([
                'guest_token',
                'session_id',
                'request_ip',
                'request_hash',
                'result_summary',
                'is_guest',
                'is_registered',
                'is_aid_user',
                'is_paid_user',
                'ads_shown',
                'is_saved',
                'source',
            ]);
        });

        DB::table('micro_tool_runs')->where('status', 'success')->update(['status' => 'completed']);
    }
};
