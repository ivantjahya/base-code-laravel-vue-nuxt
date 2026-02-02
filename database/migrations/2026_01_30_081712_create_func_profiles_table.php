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
        if (! Schema::hasTable('func_profiles')) {
            Schema::create('func_profiles', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('code', 50);
                $table->string('name');
                $table->uuid('profile_id');
                $table->uuid('merch_struct_id');
                $table->uuid('limit_id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->uuid('created_by')->nullable();
                $table->uuid('updated_by')->nullable();

                $table->unique('code');
                $table->unique(['profile_id', 'merch_struct_id']);

                $table->foreign('profile_id')->references('id')->on('profiles')->cascadeOnDelete();
                $table->foreign('merch_struct_id')->references('id')->on('merch_structs')->cascadeOnDelete();
                $table->foreign('limit_id')->references('id')->on('limits')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('func_profiles');
    }
};
