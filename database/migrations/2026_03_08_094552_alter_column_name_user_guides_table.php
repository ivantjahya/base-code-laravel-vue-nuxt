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
        if (Schema::hasColumn('user_guides', 'filename')) {
            Schema::table('user_guides', function (Blueprint $table) {
                $table->renameColumn('filename', 'file_name');
            });
        }
        if (Schema::hasColumn('user_guides', 'filepath')) {
            Schema::table('user_guides', function (Blueprint $table) {
                $table->renameColumn('filepath', 'file_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('user_guides', 'file_name')) {
            Schema::table('user_guides', function (Blueprint $table) {
                $table->renameColumn('file_name', 'filename');
            });
        }
        if (Schema::hasColumn('user_guides', 'file_path')) {
            Schema::table('user_guides', function (Blueprint $table) {
                $table->renameColumn('file_path', 'filepath');
            });
        }
    }
};
