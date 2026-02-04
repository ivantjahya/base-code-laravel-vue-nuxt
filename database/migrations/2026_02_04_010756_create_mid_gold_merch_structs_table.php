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
        Schema::create('mid_gold_merch_structs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50);
            $table->string('description');
            $table->string('parent_code', 50)->nullable();
            $table->string('parent_description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['code']);
            $table->index('code');
            $table->index('parent_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mid_gold_merch_structs');
    }
};
