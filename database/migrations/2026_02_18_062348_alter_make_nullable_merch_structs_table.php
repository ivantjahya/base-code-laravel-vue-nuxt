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
        if (Schema::hasTable('merch_structs') && Schema::hasColumn('merch_structs', 'parent_id')) {
            Schema::table('merch_structs', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
            });

            Schema::table('merch_structs', function (Blueprint $table) {
                $table->uuid('parent_id')->nullable()->change();
            });

            Schema::table('merch_structs', function (Blueprint $table) {
                $table->foreign('parent_id')->references('id')->on('merch_structs')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('merch_structs') && Schema::hasColumn('merch_structs', 'parent_id')) {
            Schema::table('merch_structs', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
            });

            Schema::table('merch_structs', function (Blueprint $table) {
                $table->uuid('parent_id')->nullable(false)->change();
            });

            Schema::table('merch_structs', function (Blueprint $table) {
                $table->foreign('parent_id')->references('id')->on('merch_structs')->cascadeOnDelete();
            });
        }
    }
};
