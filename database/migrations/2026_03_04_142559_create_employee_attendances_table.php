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
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id');
            $table->enum('employee_type', ['employee', 'admin'])->nullable()->default('employee');
            $table->timestamp('check_in_time')->useCurrent();
            $table->timestamp('check_out_time')->nullable();
            $table->boolean('is_late')->nullable()->default(false);
            $table->integer('late_minutes')->nullable()->default(0);
            $table->decimal('total_hours', 5)->nullable()->default(0);
            $table->decimal('overtime_hours', 5)->nullable()->default(0);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->enum('checkout_type', ['temporary', 'final'])->nullable()->default('final');
            $table->dateTime('temp_checkout_time')->nullable();
            $table->dateTime('temp_checkin_time')->nullable();
            $table->integer('temp_checkout_count')->nullable()->default(0);
            $table->boolean('is_temp_out')->nullable()->default(false);

            $table->index(['checkout_type', 'date'], 'idx_attendance_checkout_type');
            $table->index(['employee_id', 'date', 'is_temp_out'], 'idx_attendance_temp_status');
            $table->index(['employee_id', 'employee_type', 'date'], 'idx_employee_attendances_type_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendances');
    }
};
