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
        Schema::create('renewal_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('renewal_date');
            $table->enum('frequency', ['yearly', 'quarterly', 'monthly'])->default('yearly');
            $table->decimal('amount', 10)->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->boolean('notification_sent')->default(false);
            $table->date('last_notification_date')->nullable();
            $table->date('next_renewal_date')->nullable();
            $table->unsignedBigInteger('created_by_admin')->nullable();
            $table->unsignedBigInteger('created_by_employee')->nullable();
            $table->unsignedBigInteger('updated_by_admin')->nullable();
            $table->unsignedBigInteger('updated_by_employee')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renewal_dates');
    }
};
