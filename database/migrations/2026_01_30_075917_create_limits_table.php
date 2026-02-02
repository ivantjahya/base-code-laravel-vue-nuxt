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
        if (! Schema::hasTable('limits')) {
            Schema::create('limits', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('code', 50);
                $table->integer('min_value');
                $table->integer('max_value');
                $table->date('start_date');
                $table->date('end_date');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->uuid('created_by')->nullable();
                $table->uuid('updated_by')->nullable();

                $table->unique(['code', 'start_date', 'end_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('limits');
    }
};
