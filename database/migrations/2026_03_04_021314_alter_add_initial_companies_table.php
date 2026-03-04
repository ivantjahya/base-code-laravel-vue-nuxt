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
        if (! Schema::hasColumn('companies', 'initial')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('initial', 10)->after('status')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('companies', 'initial')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn('initial');
            });
        }
    }
};
