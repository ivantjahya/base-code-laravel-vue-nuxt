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
        if (! Schema::hasColumn('func_profiles', 'status')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->integer('status')->after('end_date')->nullable()->default(1)->comment('1: Active, 0: Inactive');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('func_profiles', 'status')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
