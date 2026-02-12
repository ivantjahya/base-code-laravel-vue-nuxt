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
        if (! Schema::hasTable('user_guides')) {
            Schema::create('user_guides', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('menu_id')->nullable();
                $table->string('name', 100);
                $table->string('description')->nullable();
                $table->string('filename');
                $table->string('filepath');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->uuid('created_by')->nullable();
                $table->uuid('updated_by')->nullable();

                $table->index('menu_id');
                $table->index('created_by');
                $table->index('updated_by');

                $table->foreign('menu_id')->references('id')->on('menus')->nullOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_guides');
    }
};
