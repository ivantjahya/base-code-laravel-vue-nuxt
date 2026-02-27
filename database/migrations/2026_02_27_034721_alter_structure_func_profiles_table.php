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
        /** Remove column */
        if (Schema::hasColumn('func_profiles', 'code')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
        if (Schema::hasColumn('func_profiles', 'name')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
        if (Schema::hasColumn('func_profiles', 'status')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        /** Add new column */
        if (! Schema::hasColumn('func_profiles', 'company_id')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->uuid('company_id')->nullable();

                $table->foreign('company_id')->references('id')->on('companies')->restrictOnDelete();
            });
        }
        if (! Schema::hasColumn('func_profiles', 'start_date')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->date('start_date')->nullable();
            });
        }
        if (! Schema::hasColumn('func_profiles', 'end_date')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->date('end_date')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('func_profiles', 'code')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->string('code', 50)->after('id');
            });
        }
        if (! Schema::hasColumn('func_profiles', 'name')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->string('name')->after('code');
            });
        }
        if (! Schema::hasColumn('func_profiles', 'status')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->integer('status')->default(1)->after('limit_code');
            });
        }
        if (Schema::hasColumn('func_profiles', 'company_id')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
        if (Schema::hasColumn('func_profiles', 'start_date')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropColumn('start_date');
            });
        }
        if (Schema::hasColumn('func_profiles', 'end_date')) {
            Schema::table('func_profiles', function (Blueprint $table) {
                $table->dropColumn('end_date');
            });
        }
    }
};
