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
        if (! Schema::hasTable('mid_gold_sites')) {
            Schema::create('mid_gold_sites', function (Blueprint $table) {
                $table->id();
                $table->string('site', 10);
                $table->string('code', 10);
                $table->string('initial', 10);
                $table->string('name');
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('region')->nullable();
                $table->integer('im_auto_flag')->default(1);
                $table->integer('dc_flag')->default(0);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['site']);
                $table->index('site');
                $table->index('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mid_gold_sites');
    }
};
