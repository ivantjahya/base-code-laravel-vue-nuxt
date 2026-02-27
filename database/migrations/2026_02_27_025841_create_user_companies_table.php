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
        if (! Schema::hasTable('user_companies')) {
            Schema::create('user_companies', function (Blueprint $table) {
                $table->uuid('user_id');
                $table->uuid('company_id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();

                $table->unique(['user_id', 'company_id']);

                $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
                $table->foreign('company_id')->references('id')->on('companies')->restrictOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_companies');
    }
};
