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
        if (Schema::hasColumn('process_logs', 'level')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->dropColumn('level');
                $table->string('level', 50);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('process_logs', 'level')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->dropColumn('level');
                $table->unsignedSmallInteger('level')->default(0);
            });
        }
    }
};
