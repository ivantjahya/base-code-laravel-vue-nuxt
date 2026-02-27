<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'login_attempt')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('login_attempt')->default(0);
            });
        }
        if (! Schema::hasColumn('users', 'status_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->uuid('status_id')->nullable()->after('login_attempt');

                $table->foreign('status_id')->references('id')->on('statuses')->restrictOnDelete();
            });

            $statusIdActive = DB::table('statuses')->where('code', 'USR_ACTIVE')->value('id');
            $statusIdInactive = DB::table('statuses')->where('code', 'USR_INACTIVE')->value('id');

            if ($statusIdActive && $statusIdInactive) {
                DB::table('users')->where('valid_date', '>=', now())->update(['status_id' => $statusIdActive]);
                DB::table('users')->where('valid_date', '<', now())->update(['status_id' => $statusIdInactive]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'login_attempt')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('login_attempt');
            });
        }
        if (Schema::hasColumn('users', 'status_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['status_id']);
                $table->dropColumn('status_id');
            });
        }
    }
};
