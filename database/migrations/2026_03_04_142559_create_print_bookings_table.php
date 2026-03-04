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
        Schema::create('print_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('work_description');
            $table->text('work_notes')->nullable();
            $table->string('client_name')->index('idx_print_bookings_client');
            $table->string('client_phone', 20)->nullable();
            $table->text('agreement')->nullable();
            $table->date('first_order')->nullable();
            $table->date('last_order')->nullable();
            $table->integer('orders_count')->nullable()->default(1);
            $table->date('initial_delivery')->nullable();
            $table->date('final_delivery')->nullable();
            $table->date('booking_date')->index('idx_print_bookings_booking_date');
            $table->dateTime('booking_time')->nullable();
            $table->integer('duration_hours')->nullable()->default(1);
            $table->string('location', 500)->nullable();
            $table->enum('status', ['in_progress', 'completed', 'bad_debt'])->index('idx_print_bookings_status');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assigned_person_id')->nullable()->index('idx_print_bookings_assigned');
            $table->unsignedBigInteger('created_by')->nullable()->index('idx_print_bookings_created_by');
            $table->unsignedBigInteger('created_by_employee')->nullable()->index('idx_print_bookings_created_by_employee');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_bookings');
    }
};
