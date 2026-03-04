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
        Schema::create('customer_communications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('potential_customer_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('voice_message')->nullable();
            $table->text('admin_reply')->nullable();
            $table->string('admin_voice_reply')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->boolean('is_read_by_admin')->default(false);
            $table->boolean('is_read_by_employee')->default(false);
            $table->string('employee_contacted_status', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_communications');
    }
};
