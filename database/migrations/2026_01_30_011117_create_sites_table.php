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
        if (! Schema::hasTable('sites')) {
            Schema::create('sites', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('site', 10);
                $table->string('code', 10);
                $table->string('initial', 10);
                $table->string('name');
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('region')->nullable();
                $table->integer('im_auto_flag')->default(1);
                $table->integer('dc_flag')->default(0);
                $table->string('code_ebs', 10)->nullable();
                $table->string('initial_ebs', 10)->nullable();
                $table->string('name_ebs')->nullable();
                $table->string('company_code_ebs')->nullable();
                $table->string('company_name_ebs')->nullable();
                $table->date('start_date');
                $table->date('end_date');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['site', 'initial', 'start_date', 'end_date']);
                $table->index('site');
                $table->index('code');
                $table->index('code_ebs');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
