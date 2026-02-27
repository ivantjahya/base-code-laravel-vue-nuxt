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
        if (! Schema::hasTable('kontrabon_regionals')) {
            Schema::create('kontrabon_regionals', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('code', 10);
                $table->string('initial', 10);
                $table->string('name');
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('region')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();

                $table->unique('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrabon_regionals');
    }
};
