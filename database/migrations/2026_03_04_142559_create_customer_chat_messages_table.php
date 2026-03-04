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
        Schema::create('customer_chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('chat_id')->index('idx_customer_chat_messages_chat');
            $table->enum('sender_type', ['admin', 'employee']);
            $table->unsignedBigInteger('sender_id');
            $table->enum('message_type', ['text', 'file', 'voice'])->default('text')->index('idx_customer_chat_messages_type');
            $table->text('content')->nullable();
            $table->string('file_path', 500)->nullable();
            $table->string('file_name')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_type', 100)->nullable();
            $table->integer('duration')->nullable()->comment('مدة التسجيل الصوتي بالثواني');
            $table->boolean('is_read')->default(false)->index('idx_customer_chat_messages_read');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent()->index('idx_customer_chat_messages_created');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();

            $table->index(['sender_type', 'sender_id'], 'idx_customer_chat_messages_sender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_chat_messages');
    }
};
