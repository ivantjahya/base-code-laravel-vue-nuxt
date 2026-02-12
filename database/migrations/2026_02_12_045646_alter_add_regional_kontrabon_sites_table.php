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
        if (! Schema::hasColumn('sites', 'regional_code_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('regional_code_kontrabon', 10)->nullable()->after('end_date');
            });
        }
        if (! Schema::hasColumn('sites', 'regional_init_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('regional_init_kontrabon', 10)->nullable()->after('regional_code_kontrabon');
            });
        }
        if (! Schema::hasColumn('sites', 'regional_name_kontrabon')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('regional_name_kontrabon')->nullable()->after('regional_init_kontrabon');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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
    }
};
