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
        if (Schema::hasColumn('process_logs', 'level_name')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->dropColumn('level_name');
            });
        }
        if (Schema::hasColumn('process_logs', 'datetime')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->dropColumn('datetime');
            });
        }
        if (Schema::hasColumn('process_logs', 'extra')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->dropColumn('extra');
            });
        }
        if (Schema::hasColumn('process_logs', 'context')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->dropColumn('context');
                $table->json('context')->nullable();
            });
        }
        if (Schema::hasTable('process_logs')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->uuid('request_id')->nullable();
                $table->string('method', 10)->nullable();
                $table->string('path')->nullable();
                $table->integer('status_code')->nullable();
                $table->integer('duration_ms')->nullable();
                $table->text('exception')->nullable();

                $table->index('request_id');
                $table->index(['channel', 'level', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('process_logs', 'level_name')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->string('level_name', 20)->nullable();
            });
        }
        if (! Schema::hasColumn('process_logs', 'datetime')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->string('datetime')->nullable();
            });
        }
        if (! Schema::hasColumn('process_logs', 'extra')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->text('extra')->nullable();
            });
        }
        if (! Schema::hasColumn('process_logs', 'context')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->text('context')->nullable();
            });
        }
        if (Schema::hasColumn('process_logs', 'request_id')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->dropColumn('request_id');
            });
        }
        if (Schema::hasColumn('process_logs', 'method')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->dropColumn('method');
            });
        }
        if (Schema::hasColumn('process_logs', 'path')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->dropColumn('path');
            });
        }
        if (Schema::hasColumn('process_logs', 'status_code')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->dropColumn('status_code');
            });
        }
        if (Schema::hasColumn('process_logs', 'duration_ms')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->dropColumn('duration_ms');
            });
        }
        if (Schema::hasColumn('process_logs', 'exception')) {
            Schema::table('process_logs', function (Blueprint $table) {
                $table->dropColumn('exception');
            });
        }
    }
};
