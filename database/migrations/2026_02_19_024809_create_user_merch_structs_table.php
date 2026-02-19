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
        if (! Schema::hasTable('user_merch_structs')) {
            Schema::create('user_merch_structs', function (Blueprint $table) {
                $table->uuid('user_id');
                $table->uuid('merch_struct_id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['user_id', 'merch_struct_id']);

                $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
                $table->foreign('merch_struct_id')->references('id')->on('merch_structs')->restrictOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_merch_structs');
    }
};
