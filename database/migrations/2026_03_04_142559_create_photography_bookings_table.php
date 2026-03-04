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
        Schema::create('photography_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('work_description');
            $table->text('work_notes')->nullable();
            $table->string('client_name');
            $table->string('client_phone', 20)->nullable();
            $table->text('agreement')->nullable();
            $table->date('first_session')->nullable();
            $table->date('last_session')->nullable();
            $table->integer('sessions_count')->nullable()->default(1);
            $table->date('montage_start')->nullable();
            $table->date('initial_delivery')->nullable();
            $table->date('final_delivery')->nullable();
            $table->date('booking_date');
            $table->dateTime('booking_time')->nullable();
            $table->integer('duration_hours')->nullable()->default(1);
            $table->string('location', 500)->nullable();
            $table->enum('status', ['in_progress', 'completed', 'bad_debt']);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assigned_person_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('created_by_employee')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photography_bookings');
    }
};
