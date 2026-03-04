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
        Schema::create('potential_customer_classification_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('potential_customer_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('classification_name');
            $table->enum('action', ['added', 'removed']);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('potential_customer_classification_history');
    }
};
