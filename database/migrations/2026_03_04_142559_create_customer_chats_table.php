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
        Schema::create('customer_chats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('potential_customer_id')->index('idx_customer_chats_customer');
            $table->unsignedBigInteger('admin_id')->nullable()->index('idx_customer_chats_admin');
            $table->unsignedBigInteger('employee_id')->index('idx_customer_chats_employee');
            $table->enum('status', ['pending', 'active', 'completed', 'closed'])->default('pending')->index('idx_customer_chats_status');
            $table->enum('priority', ['low', 'normal', 'medium', 'high'])->default('normal')->index('idx_customer_chats_priority');
            $table->enum('customer_type', ['general', 'call_request', 'visit_request'])->default('general')->index('idx_customer_chats_type');
            $table->timestamp('last_message_at')->nullable()->index('idx_customer_chats_last_message');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_chats');
    }
};
