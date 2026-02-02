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
        if (! Schema::hasTable('merch_structs')) {
            Schema::create('merch_structs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('code', 50);
                $table->string('name');
                $table->uuid('parent_id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();

                $table->unique('id');
                $table->unique('code');

                $table->foreign('parent_id')->references('id')->on('merch_structs')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merch_structs');
    }
};
