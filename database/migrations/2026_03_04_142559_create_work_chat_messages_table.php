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
        Schema::create('work_chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('chat_id')->comment('معرف الشات');
            $table->enum('sender_type', ['admin', 'employee'])->comment('نوع المرسل');
            $table->unsignedBigInteger('sender_id')->comment('معرف المرسل');
            $table->enum('message_type', ['text', 'voice', 'file'])->default('text')->comment('نوع الرسالة');
            $table->text('content')->nullable()->comment('محتوى الرسالة النصية');
            $table->string('file_path', 500)->nullable()->comment('مسار الملف');
            $table->string('file_name')->nullable()->comment('اسم الملف الأصلي');
            $table->bigInteger('file_size')->nullable()->comment('حجم الملف بالبايت');
            $table->string('file_type', 100)->nullable()->comment('نوع الملف');
            $table->integer('duration')->nullable()->comment('مدة التسجيل الصوتي بالثواني');
            $table->boolean('is_read')->default(false)->comment('هل تم قراءة الرسالة');
            $table->timestamp('read_at')->nullable()->comment('وقت القراءة');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_chat_messages');
    }
};
