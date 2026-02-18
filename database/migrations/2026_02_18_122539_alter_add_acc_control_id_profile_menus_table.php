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
        if (! Schema::hasColumn('profile_menus', 'acc_control_id')) {
            Schema::table('profile_menus', function (Blueprint $table) {
                $table->uuid('acc_control_id')->after('menu_id')->nullable();

                $table->dropUnique('profile_menus_profile_id_menu_id_unique');
                $table->unique(['profile_id', 'menu_id', 'acc_control_id']);

                $table->foreign('acc_control_id')->references('id')->on('access_controls')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('profile_menus', 'acc_control_id')) {
            Schema::table('profile_menus', function (Blueprint $table) {
                $table->dropForeign(['acc_control_id']);
                $table->dropUnique(['profile_id', 'menu_id', 'acc_control_id']);
                $table->dropColumn('acc_control_id');
            });
        }
    }
};
