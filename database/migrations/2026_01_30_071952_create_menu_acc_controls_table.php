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
        if (! Schema::hasTable('menu_acc_controls')) {
            Schema::create('menu_acc_controls', function (Blueprint $table) {
                $table->uuid('menu_id');
                $table->uuid('acc_control_id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();

                $table->foreign('menu_id')->references('id')->on('menus')->cascadeOnDelete();
                $table->foreign('acc_control_id')->references('id')->on('access_controls')->cascadeOnDelete();

                $table->unique(['menu_id', 'acc_control_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_acc_controls');
    }
};
