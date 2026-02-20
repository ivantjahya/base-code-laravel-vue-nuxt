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
        if (Schema::hasTable('user_merch_structs')) {
            DB::statement('UPDATE user_merch_structs SET updated_at = created_at WHERE updated_at IS NULL');
            Schema::table('user_merch_structs', function (Blueprint $table) {
                $table->timestamp('updated_at')->useCurrent()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_merch_structs')) {
            Schema::table('user_merch_structs', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->change();
            });
        }
    }
};
