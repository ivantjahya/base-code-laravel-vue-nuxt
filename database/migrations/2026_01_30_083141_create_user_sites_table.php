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
        if (! Schema::hasTable('user_sites')) {
            Schema::create('user_sites', function (Blueprint $table) {
                $table->uuid('user_id');
                $table->uuid('site_id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->uuid('created_by')->nullable();
                $table->uuid('updated_by')->nullable();

                $table->unique(['user_id', 'site_id']);

                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('site_id')->references('id')->on('sites')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sites');
    }
};
