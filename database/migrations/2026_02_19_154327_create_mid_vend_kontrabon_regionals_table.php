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
        if (! Schema::hasTable('mid_vend_kontrabon_regionals')) {
            Schema::create('mid_vend_kontrabon_regionals', function (Blueprint $table) {
                $table->id();
                $table->string('site', 10);
                $table->string('regional_code_kontrabon', 10);
                $table->string('regional_init_kontrabon', 10);
                $table->string('regional_name_kontrabon');
                $table->integer('status')->default(0)->comment('0: Not processed, 1: Processed, 2: Failed processed');
                $table->string('description')->nullable()->comment('Description for status or error message if failed processed');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();

                $table->unique(['site']);
                $table->index('site');
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mid_vend_kontrabon_regionals');
    }
};
