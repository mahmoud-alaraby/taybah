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
        Schema::table('customer_chat_messages', function (Blueprint $table) {
            $table->foreign(['chat_id'], 'fk_customer_chat_messages_chat')->references(['id'])->on('customer_chats')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_chat_messages', function (Blueprint $table) {
            $table->dropForeign('fk_customer_chat_messages_chat');
        });
    }
};
