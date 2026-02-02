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
        if (! Schema::hasTable('statuses')) {
            Schema::create('statuses', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('code', 50);
                $table->string('description');
                $table->string('used_for', 100)->nullable();
                $table->string('status_group', 100)->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['code', 'used_for', 'status_group']);
                $table->index('used_for');
                $table->index('status_group');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
