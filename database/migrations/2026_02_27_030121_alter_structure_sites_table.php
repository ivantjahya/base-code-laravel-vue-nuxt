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
        /** Company */
        if (Schema::hasColumn('sites', 'company_code_ebs')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->dropColumn('company_code_ebs');
            });
        }
        if (Schema::hasColumn('sites', 'company_name_ebs')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->dropColumn('company_name_ebs');
            });
        }
        if (! Schema::hasColumn('sites', 'company_id')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->uuid('company_id')->nullable();

                $table->foreign('company_id')->references('id')->on('companies')->restrictOnDelete();
            });
        }

        /** Regional Kontrabon */
        if (Schema::hasColumn('sites', 'regional_code_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->dropColumn('regional_code_kontrabon');
            });
        }
        if (Schema::hasColumn('sites', 'regional_init_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->dropColumn('regional_init_kontrabon');
            });
        }
        if (Schema::hasColumn('sites', 'regional_name_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->dropColumn('regional_name_kontrabon');
            });
        }
        if (Schema::hasColumn('sites', 'regional_address_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->dropColumn('regional_address_kontrabon');
            });
        }
        if (Schema::hasColumn('sites', 'regional_city_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->dropColumn('regional_city_kontrabon');
            });
        }
        if (Schema::hasColumn('sites', 'regional_region_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->dropColumn('regional_region_kontrabon');
            });
        }
        if (! Schema::hasColumn('sites', 'kontrabon_regional_id')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->uuid('kontrabon_regional_id')->nullable();

                $table->foreign('kontrabon_regional_id')->references('id')->on('kontrabon_regionals')->restrictOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /** Company */
        if (!Schema::hasColumn('sites', 'company_code_ebs')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('company_code_ebs')->nullable();
            });
        }
        if (!Schema::hasColumn('sites', 'company_name_ebs')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('company_name_ebs')->nullable();
            });
        }
        if (Schema::hasColumn('sites', 'company_id')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        /** Regional Kontrabon */
        if (!Schema::hasColumn('sites', 'regional_code_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('regional_code_kontrabon', 10)->nullable();
            });
        }
        if (!Schema::hasColumn('sites', 'regional_init_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('regional_init_kontrabon', 10)->nullable();
            });
        }
        if (!Schema::hasColumn('sites', 'regional_name_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('regional_name_kontrabon')->nullable();
            });
        }
        if (!Schema::hasColumn('sites', 'regional_address_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('regional_address_kontrabon')->nullable();
            });
        }
        if (!Schema::hasColumn('sites', 'regional_city_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('regional_city_kontrabon')->nullable();
            });
        }
        if (!Schema::hasColumn('sites', 'regional_region_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('regional_region_kontrabon')->nullable();
            });
        }
        if (Schema::hasColumn('sites', 'kontrabon_regional_id')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->dropForeign(['kontrabon_regional_id']);
                $table->dropColumn('kontrabon_regional_id');
            });
        }
    }
};
