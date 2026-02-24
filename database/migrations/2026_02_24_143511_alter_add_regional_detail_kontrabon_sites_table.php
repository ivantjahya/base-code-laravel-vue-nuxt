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
        if (! Schema::hasColumn('mid_vend_kontrabon_regionals', 'regional_address_kontrabon')) {
            Schema::table('mid_vend_kontrabon_regionals', function (Blueprint $table) {
                $table->string('regional_address_kontrabon')->nullable();
            });
        }
        if (! Schema::hasColumn('mid_vend_kontrabon_regionals', 'regional_city_kontrabon')) {
            Schema::table('mid_vend_kontrabon_regionals', function (Blueprint $table) {
                $table->string('regional_city_kontrabon')->nullable();
            });
        }
        if (! Schema::hasColumn('mid_vend_kontrabon_regionals', 'regional_region_kontrabon')) {
            Schema::table('mid_vend_kontrabon_regionals', function (Blueprint $table) {
                $table->string('regional_region_kontrabon')->nullable();
            });
        }
        if (! Schema::hasColumn('sites', 'regional_address_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('regional_address_kontrabon')->nullable();
            });
        }
        if (! Schema::hasColumn('sites', 'regional_city_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('regional_city_kontrabon')->nullable();
            });
        }
        if (! Schema::hasColumn('sites', 'regional_region_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('regional_region_kontrabon')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('mid_vend_kontrabon_regionals', 'regional_address_kontrabon')) {
            Schema::table('mid_vend_kontrabon_regionals', function (Blueprint $table) {
                $table->dropColumn('regional_address_kontrabon');
            });
        }
        if (Schema::hasColumn('mid_vend_kontrabon_regionals', 'regional_city_kontrabon')) {
            Schema::table('mid_vend_kontrabon_regionals', function (Blueprint $table) {
                $table->dropColumn('regional_city_kontrabon');
            });
        }
        if (Schema::hasColumn('mid_vend_kontrabon_regionals', 'regional_region_kontrabon')) {
            Schema::table('mid_vend_kontrabon_regionals', function (Blueprint $table) {
                $table->dropColumn('regional_region_kontrabon');
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
    }
};
