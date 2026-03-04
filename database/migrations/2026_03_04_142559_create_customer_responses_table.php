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
        Schema::create('customer_responses', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('category_id');
            $table->string('title', 80);
            $table->text('body');
            $table->unsignedBigInteger('created_by');
            $table->enum('created_by_type', ['admin', 'employee']);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_responses');
    }
};
