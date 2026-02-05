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
        if (! Schema::hasTable('mid_gold_site_networks')) {
            Schema::create('mid_gold_site_networks', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_code');
                $table->integer('child_code');
                $table->string('type', 100);
                $table->string('comm_network', 100);
                $table->string('comm_network_desc', 100);
                $table->integer('site_class');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['parent_code', 'child_code']);
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
        Schema::dropIfExists('mid_gold_site_networks');
    }
};
