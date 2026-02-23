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
        if (! Schema::hasIndex('func_profiles', 'uq_profile_merch_struct')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->unique(['profile_id', 'merch_struct_id'], 'uq_profile_merch_struct');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('func_profiles', 'uq_profile_merch_struct')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropUnique('uq_profile_merch_struct');
            });
        }
    }
};
