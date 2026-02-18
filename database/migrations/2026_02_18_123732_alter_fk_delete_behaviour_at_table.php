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
        if (Schema::hasTable('menus')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->foreign('parent_id')->references('id')->on('menus')->restrictOnDelete();
            });
        }
        if (Schema::hasTable('menu_acc_controls')) {
            Schema::table('menu_acc_controls', function (Blueprint $table) {
                $table->dropForeign(['menu_id']);
                $table->dropForeign(['acc_control_id']);
                $table->foreign('menu_id')->references('id')->on('menus')->restrictOnDelete();
                $table->foreign('acc_control_id')->references('id')->on('access_controls')->restrictOnDelete();
            });
        }
        if (Schema::hasTable('merch_structs')) {
            Schema::table('merch_structs', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->foreign('parent_id')->references('id')->on('merch_structs')->restrictOnDelete();
            });
        }
        if (Schema::hasTable('func_profiles')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropForeign(['profile_id']);
                $table->dropForeign(['merch_struct_id']);
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->foreign('profile_id')->references('id')->on('profiles')->restrictOnDelete();
                $table->foreign('merch_struct_id')->references('id')->on('merch_structs')->restrictOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
            });
        }
        if (Schema::hasTable('profile_menus')) {
            Schema::table('profile_menus', function (Blueprint $table) {
                $table->dropForeign(['profile_id']);
                $table->dropForeign(['menu_id']);
                $table->dropForeign(['acc_control_id']);
                $table->foreign('profile_id')->references('id')->on('profiles')->restrictOnDelete();
                $table->foreign('menu_id')->references('id')->on('menus')->restrictOnDelete();
                $table->foreign('acc_control_id')->references('id')->on('access_controls')->restrictOnDelete();
            });
        }
        if (Schema::hasTable('approval_po_flows')) {
            Schema::table('approval_po_flows', function (Blueprint $table) {
                $table->dropForeign(['merch_struct_id']);
                $table->dropForeign(['profile_id']);
                $table->dropForeign(['po_status_id']);
                $table->dropForeign(['next_profile_id']);
                $table->dropForeign(['next_po_status_id']);
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->foreign('merch_struct_id')->references('id')->on('merch_structs')->restrictOnDelete();
                $table->foreign('profile_id')->references('id')->on('profiles')->restrictOnDelete();
                $table->foreign('po_status_id')->references('id')->on('statuses')->restrictOnDelete();
                $table->foreign('next_profile_id')->references('id')->on('profiles')->restrictOnDelete();
                $table->foreign('next_po_status_id')->references('id')->on('statuses')->restrictOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
            });
        }
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['profile_id']);
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->foreign('profile_id')->references('id')->on('profiles')->restrictOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
            });
        }
        if (Schema::hasTable('user_sites')) {
            Schema::table('user_sites', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['site_id']);
                $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
                $table->foreign('site_id')->references('id')->on('sites')->restrictOnDelete();
            });
        }
        if (Schema::hasTable('user_guides')) {
            Schema::table('user_guides', function (Blueprint $table) {
                $table->dropForeign(['menu_id']);
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->foreign('menu_id')->references('id')->on('menus')->restrictOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
            });
        }
        if (Schema::hasTable('limits')) {
            Schema::table('limits', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
            });
        }
        if (Schema::hasTable('profiles')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('menus')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->foreign('parent_id')->references('id')->on('menus')->nullOnDelete();
            });
        }
        if (Schema::hasTable('menu_acc_controls')) {
            Schema::table('menu_acc_controls', function (Blueprint $table) {
                $table->dropForeign(['menu_id']);
                $table->dropForeign(['acc_control_id']);
                $table->foreign('menu_id')->references('id')->on('menus')->cascadeOnDelete();
                $table->foreign('acc_control_id')->references('id')->on('access_controls')->cascadeOnDelete();
            });
        }
        if (Schema::hasTable('merch_structs')) {
            Schema::table('merch_structs', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->foreign('parent_id')->references('id')->on('merch_structs')->cascadeOnDelete();
            });
        }
        if (Schema::hasTable('func_profiles')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropForeign(['profile_id']);
                $table->dropForeign(['merch_struct_id']);
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->foreign('profile_id')->references('id')->on('profiles')->cascadeOnDelete();
                $table->foreign('merch_struct_id')->references('id')->on('merch_structs')->cascadeOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            });
        }
        if (Schema::hasTable('profile_menus')) {
            Schema::table('profile_menus', function (Blueprint $table) {
                $table->dropForeign(['profile_id']);
                $table->dropForeign(['menu_id']);
                $table->dropForeign(['acc_control_id']);
                $table->foreign('profile_id')->references('id')->on('profiles')->cascadeOnDelete();
                $table->foreign('menu_id')->references('id')->on('menus')->cascadeOnDelete();
                $table->foreign('acc_control_id')->references('id')->on('access_controls')->cascadeOnDelete();
            });
        }
        if (Schema::hasTable('approval_po_flows')) {
            Schema::table('approval_po_flows', function (Blueprint $table) {
                $table->dropForeign(['merch_struct_id']);
                $table->dropForeign(['profile_id']);
                $table->dropForeign(['po_status_id']);
                $table->dropForeign(['next_profile_id']);
                $table->dropForeign(['next_po_status_id']);
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->foreign('merch_struct_id')->references('id')->on('merch_structs')->cascadeOnDelete();
                $table->foreign('profile_id')->references('id')->on('profiles')->cascadeOnDelete();
                $table->foreign('po_status_id')->references('id')->on('statuses')->nullOnDelete();
                $table->foreign('next_profile_id')->references('id')->on('profiles')->nullOnDelete();
                $table->foreign('next_po_status_id')->references('id')->on('statuses')->nullOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            });
        }
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['profile_id']);
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->foreign('profile_id')->references('id')->on('profiles')->nullOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            });
        }
        if (Schema::hasTable('user_sites')) {
            Schema::table('user_sites', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['site_id']);
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('site_id')->references('id')->on('sites')->cascadeOnDelete();
            });
        }
        if (Schema::hasTable('user_guides')) {
            Schema::table('user_guides', function (Blueprint $table) {
                $table->dropForeign(['menu_id']);
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->foreign('menu_id')->references('id')->on('menus')->nullOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            });
        }
        if (Schema::hasTable('limits')) {
            Schema::table('limits', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            });
        }
        if (Schema::hasTable('profiles')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }
};
