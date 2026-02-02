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
        if (! Schema::hasTable('approval_po_flows')) {
            Schema::create('approval_po_flows', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('code', 50);
                $table->string('name');
                $table->uuid('merch_struct_id');
                $table->uuid('profile_id');
                $table->uuid('po_status_id');
                $table->uuid('next_profile_id');
                $table->uuid('next_po_status_id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->uuid('created_by')->nullable();
                $table->uuid('updated_by')->nullable();

                $table->unique('code');
                $table->unique(['merch_struct_id', 'profile_id']);

                $table->foreign('merch_struct_id')->references('id')->on('merch_structs')->cascadeOnDelete();
                $table->foreign('profile_id')->references('id')->on('profiles')->cascadeOnDelete();
                $table->foreign('po_status_id')->references('id')->on('statuses')->nullOnDelete();
                $table->foreign('next_profile_id')->references('id')->on('profiles')->nullOnDelete();
                $table->foreign('next_po_status_id')->references('id')->on('statuses')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_po_flows');
    }
};
