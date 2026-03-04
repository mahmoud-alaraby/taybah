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
        Schema::create('time_trackings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id');
            $table->enum('employee_type', ['employee', 'admin'])->nullable()->default('employee');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('task_id');
            $table->timestamp('start_time')->useCurrent();
            $table->timestamp('end_time')->nullable();
            $table->integer('total_seconds')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->date('date');
            $table->boolean('is_active')->nullable()->default(true);
            $table->boolean('is_paused')->nullable()->default(false);
            $table->integer('pause_count')->nullable()->default(0);
            $table->integer('resume_count')->nullable()->default(0);
            $table->integer('session_number')->nullable()->default(1);
            $table->unsignedBigInteger('parent_session_id')->nullable()->index();
            $table->longText('pause_resume_log')->nullable();
            $table->boolean('is_editable')->nullable()->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->string('edited_by_type')->nullable();
            $table->integer('original_seconds')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();

            $table->index(['employee_id', 'employee_type', 'date'], 'idx_time_trackings_type_date');
            $table->index(['is_active', 'is_paused']);
            $table->index(['task_id', 'session_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_trackings');
    }
};
