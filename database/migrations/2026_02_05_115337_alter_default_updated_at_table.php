<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('sites')) {
            DB::statement('UPDATE sites SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('sites', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('site_networks')) {
            DB::statement('UPDATE site_networks SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('site_networks', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('menus')) {
            DB::statement('UPDATE menus SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('menus', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('access_controls')) {
            DB::statement('UPDATE access_controls SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('access_controls', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('menu_acc_controls')) {
            DB::statement('UPDATE menu_acc_controls SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('menu_acc_controls', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('statuses')) {
            DB::statement('UPDATE statuses SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('statuses', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('limits')) {
            DB::statement('UPDATE limits SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('limits', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('merch_structs')) {
            DB::statement('UPDATE merch_structs SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('merch_structs', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('profiles')) {
            DB::statement('UPDATE profiles SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('profiles', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('func_profiles')) {
            DB::statement('UPDATE func_profiles SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('profile_menus')) {
            DB::statement('UPDATE profile_menus SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('profile_menus', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('approval_po_flows')) {
            DB::statement('UPDATE approval_po_flows SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('approval_po_flows', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('users')) {
            DB::statement('UPDATE users SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('user_sites')) {
            DB::statement('UPDATE user_sites SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('user_sites', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('user_logs')) {
            DB::statement('UPDATE user_logs SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('user_logs', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
        if (Schema::hasTable('process_logs')) {
            DB::statement('UPDATE process_logs SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('process_logs', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sites')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('site_networks')) {
            Schema::table('site_networks', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('menus')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('access_controls')) {
            Schema::table('access_controls', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('menu_acc_controls')) {
            Schema::table('menu_acc_controls', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('statuses')) {
            Schema::table('statuses', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('limits')) {
            Schema::table('limits', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('merch_structs')) {
            Schema::table('merch_structs', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('profiles')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('func_profiles')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('profile_menus')) {
            Schema::table('profile_menus', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('approval_po_flows')) {
            Schema::table('approval_po_flows', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('user_sites')) {
            Schema::table('user_sites', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('user_logs')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
        if (Schema::hasTable('process_logs')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
    }
};
