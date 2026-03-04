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
        Schema::create('print_monthly_targets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id')->index('print_monthly_targets_employee_id_foreign');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('target_amount', 10);
            $table->timestamps();

            $table->unique(['employee_id', 'year', 'month'], 'print_monthly_targets_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_monthly_targets');
    }
};
