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
        Schema::table('users', function (Blueprint $table): void {
            $table->string('aid', 26)->nullable()->unique()->after('id');
        });

        foreach (DB::table('users')->whereNull('aid')->cursor() as $user) {
            DB::table('users')->where('id', $user->id)->update(['aid' => (string) Str::ulid()]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('aid');
        });
    }
};
