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
        Schema::create('customer_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->text('work_description');
            $table->date('agreement_start_date');
            $table->date('initial_delivery_date');
            $table->date('final_delivery_date')->nullable();
            $table->decimal('agreed_amount', 10)->default(0);
            $table->decimal('first_payment', 10)->default(0);
            $table->decimal('second_payment', 10)->default(0);
            $table->decimal('third_payment', 10)->default(0);
            $table->decimal('fourth_payment', 10)->default(0);
            $table->enum('customer_type', ['C', 'B', 'A', 'A+', 'A++', 'غير محدد'])->default('غير محدد');
            $table->enum('work_status', ['تم الانسحاب', 'لم يتم الاكمال', 'تم الانتهاء', 'ديون معدومة', 'جاري العمل'])->default('جاري العمل');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_movements');
    }
};
