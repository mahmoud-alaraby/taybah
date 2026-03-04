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
        Schema::create('daily_work_summaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id');
            $table->enum('employee_type', ['employee', 'admin'])->nullable()->default('employee');
            $table->date('date');
            $table->decimal('total_work_hours', 5)->nullable()->default(0);
            $table->decimal('overtime_hours', 5)->nullable()->default(0);
            $table->longText('projects_worked')->nullable();
            $table->decimal('daily_target_percentage', 5)->nullable()->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();

            $table->index(['employee_id', 'employee_type', 'date'], 'idx_daily_work_summaries_type_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_work_summaries');
    }
};
