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
        if (Schema::hasIndex('func_profiles', 'func_profiles_profile_id_merch_struct_id_unique')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropUnique('func_profiles_profile_id_merch_struct_id_unique');
            });
        }
        if (Schema::hasIndex('func_profiles', 'uq_profile_merch_struct')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropUnique('uq_profile_merch_struct');
            });
        }
        if (! Schema::hasIndex('func_profiles', 'func_profiles_profile_id_company_id_merch_struct_id_unique')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->unique(['profile_id', 'company_id', 'merch_struct_id'], 'func_profiles_profile_id_company_id_merch_struct_id_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('func_profiles', 'func_profiles_profile_id_company_id_merch_struct_id_unique')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropUnique('func_profiles_profile_id_company_id_merch_struct_id_unique');
            });
        }
        if (! Schema::hasIndex('func_profiles', 'uq_profile_merch_struct')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->unique(['profile_id', 'merch_struct_id'], 'uq_profile_merch_struct');
            });
        }
        if (! Schema::hasIndex('func_profiles', 'func_profiles_profile_id_merch_struct_id_unique')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->unique(['profile_id', 'merch_struct_id'], 'func_profiles_profile_id_merch_struct_id_unique');
            });
        }
    }
};
