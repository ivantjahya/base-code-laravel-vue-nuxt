<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('func_profiles', 'limit_id')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropColumn('limit_id');
            });
        }
        if (! Schema::hasColumn('func_profiles', 'limit_code')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->string('limit_code')->after('merch_struct_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('func_profiles', 'limit_code')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropColumn('limit_code');
            });
        }
        if (! Schema::hasColumn('func_profiles', 'limit_id')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->uuid('limit_id')->nullable();
            });
        }
    }
};
