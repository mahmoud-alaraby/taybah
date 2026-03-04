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
        Schema::create('operation_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('task_date');
            $table->enum('task_type', ['design', 'marketing']);
            $table->unsignedBigInteger('assigned_person_id');
            $table->text('task_description')->nullable();
            $table->boolean('is_reserved')->nullable()->default(false);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->nullable()->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_tasks');
    }
};
