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
        if (! Schema::hasColumn('menus', 'name_code')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->string('name_code', 100)->after('code')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('menus', 'name_code')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn('name_code');
            });
        }
    }
};
