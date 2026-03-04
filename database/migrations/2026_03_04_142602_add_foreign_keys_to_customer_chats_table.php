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
        Schema::table('customer_chats', function (Blueprint $table) {
            $table->foreign(['admin_id'], 'fk_customer_chats_admin')->references(['id'])->on('admins')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['employee_id'], 'fk_customer_chats_employee')->references(['id'])->on('employees')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['potential_customer_id'], 'fk_customer_chats_potential_customer')->references(['id'])->on('potential_customers')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_chats', function (Blueprint $table) {
            $table->dropForeign('fk_customer_chats_admin');
            $table->dropForeign('fk_customer_chats_employee');
            $table->dropForeign('fk_customer_chats_potential_customer');
        });
    }
};
