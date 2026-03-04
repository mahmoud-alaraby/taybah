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
        Schema::create('work_chats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->comment('عنوان الشات');
            $table->enum('type', ['design', 'montage'])->comment('نوع الشات - design أو montage');
            $table->unsignedBigInteger('admin_id')->nullable()->comment('المدير المشارك');
            $table->unsignedBigInteger('employee_id')->nullable()->comment('الموظف المشارك');
            $table->enum('status', ['active', 'completed', 'archived'])->default('active')->comment('حالة الشات');
            $table->timestamp('last_message_at')->nullable()->comment('آخر رسالة');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_chats');
    }
};
