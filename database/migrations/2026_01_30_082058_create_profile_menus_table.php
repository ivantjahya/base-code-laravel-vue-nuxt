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
        if (! Schema::hasTable('profile_menus')) {
            Schema::create('profile_menus', function (Blueprint $table) {
                $table->uuid('profile_id');
                $table->uuid('menu_id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['profile_id', 'menu_id']);

                $table->foreign('profile_id')->references('id')->on('profiles')->cascadeOnDelete();
                $table->foreign('menu_id')->references('id')->on('menus')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_menus');
    }
};
