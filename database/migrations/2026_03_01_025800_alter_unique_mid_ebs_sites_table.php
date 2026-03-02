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
        if (Schema::hasIndex('mid_ebs_sites', 'mid_ebs_sites_code_unique')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->dropUnique('mid_ebs_sites_code_unique');
            });
        }
        if (Schema::hasColumn('mid_ebs_sites', 'site')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->unique(['site']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasIndex('mid_ebs_sites', 'mid_ebs_sites_code_unique')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->unique(['code']);
            });
        }
        if (Schema::hasColumn('mid_ebs_sites', 'site')) {
            Schema::table('mid_ebs_sites', function (Blueprint $table) {
                $table->dropUnique(['site']);
            });
        }
    }
};
