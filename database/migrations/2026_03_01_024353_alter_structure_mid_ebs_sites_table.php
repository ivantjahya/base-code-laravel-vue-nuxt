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
        /** Alter max length */
        if (Schema::hasColumn('mid_ebs_sites', 'company_code')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->string('company_code', 50)->change();
            });
        }

        /** Add new columns */
        if (! Schema::hasColumn('mid_ebs_sites', 'site')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->string('site')->nullable();
            });
        }
        if (! Schema::hasColumn('mid_ebs_sites', 'company_address')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->string('company_address')->nullable();
            });
        }
        if (! Schema::hasColumn('mid_ebs_sites', 'company_city')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->string('company_city')->nullable();
            });
        }
        if (! Schema::hasColumn('mid_ebs_sites', 'company_region')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->string('company_region')->nullable();
            });
        }
        if (! Schema::hasColumn('mid_ebs_sites', 'regional_code')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->string('regional_code', 50)->nullable();
            });
        }
        if (! Schema::hasColumn('mid_ebs_sites', 'regional_name')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->string('regional_name')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('mid_ebs_sites', 'company_code')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->string('company_code', 255)->change();
            });
        }
        if (Schema::hasColumn('mid_ebs_sites', 'site')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->dropColumn('site');
            });
        }
        if (Schema::hasColumn('mid_ebs_sites', 'company_address')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->dropColumn('company_address');
            });
        }
        if (Schema::hasColumn('mid_ebs_sites', 'company_city')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->dropColumn('company_city');
            });
        }
        if (Schema::hasColumn('mid_ebs_sites', 'company_region')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->dropColumn('company_region');
            });        
        }
        if (Schema::hasColumn('mid_ebs_sites', 'regional_code')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->dropColumn('regional_code');
            });
        }
        if (Schema::hasColumn('mid_ebs_sites', 'regional_name')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->dropColumn('regional_name');
            });
        }
    }
};
