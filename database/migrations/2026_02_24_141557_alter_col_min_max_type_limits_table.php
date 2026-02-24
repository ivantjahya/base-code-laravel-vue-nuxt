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
        Schema::table('limits', function (Blueprint $table) {
            if (Schema::hasColumn('limits', 'min_value')) {
                $table->bigInteger('min_value')->nullable()->change();
            }
            if (Schema::hasColumn('limits', 'max_value')) {
                $table->bigInteger('max_value')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('limits', function (Blueprint $table) {
            if (Schema::hasColumn('limits', 'min_value')) {
                $table->integer('min_value')->nullable()->change();
            }
            if (Schema::hasColumn('limits', 'max_value')) {
                $table->integer('max_value')->nullable()->change();
            }
        });
    }
};
