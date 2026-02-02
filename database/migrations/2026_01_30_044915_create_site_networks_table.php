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
        if (! Schema::hasTable('site_networks')) {
            Schema::create('site_networks', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->integer('parent_code');
                $table->integer('child_code');
                $table->string('type');
                $table->string('comm_network');
                $table->string('comm_network_desc');
                $table->integer('site_class');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();

                $table->index('parent_code');
                $table->index('child_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_networks');
    }
};
