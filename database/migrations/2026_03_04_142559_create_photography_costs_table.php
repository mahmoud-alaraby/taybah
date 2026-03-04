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
        Schema::create('photography_costs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('date')->index();
            $table->decimal('amount', 12);
            $table->enum('type', ['receipt', 'payment'])->index();
            $table->string('note')->nullable();
            $table->integer('created_by');
            $table->enum('created_by_type', ['employee', 'admin']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();

            $table->index(['created_by_type', 'created_by']);
            $table->index(['date', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photography_costs');
    }
};
