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
        Schema::create('designer_task_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('designer_id');
            $table->date('task_date');
            $table->string('task1')->nullable();
            $table->decimal('price1', 10)->nullable();
            $table->text('desc1')->nullable();
            $table->string('task2')->nullable();
            $table->decimal('price2', 10)->nullable();
            $table->text('desc2')->nullable();
            $table->string('task3')->nullable();
            $table->decimal('price3', 10)->nullable();
            $table->text('desc3')->nullable();
            $table->string('task4')->nullable();
            $table->decimal('price4', 10)->nullable();
            $table->text('desc4')->nullable();
            $table->string('task5')->nullable();
            $table->decimal('price5', 10)->nullable();
            $table->text('desc5')->nullable();
            $table->decimal('total_price', 10)->nullable()->storedAs('((((coalesce(`price1`,0) + coalesce(`price2`,0)) + coalesce(`price3`,0)) + coalesce(`price4`,0)) + coalesce(`price5`,0))');
            $table->unsignedBigInteger('created_by_admin')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designer_task_accounts');
    }
};
