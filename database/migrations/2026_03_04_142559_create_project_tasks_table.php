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
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('estimated_hours', 5)->nullable()->default(0);
            $table->decimal('actual_hours', 5)->nullable()->default(0);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'paused'])->nullable()->default('pending');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->enum('assigned_to_type', ['employee', 'admin'])->nullable()->default('employee');
            $table->unsignedBigInteger('created_by');
            $table->enum('created_by_type', ['admin', 'employee']);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();

            $table->index(['assigned_to', 'assigned_to_type'], 'idx_project_tasks_assigned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};
