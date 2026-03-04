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
        Schema::table('time_trackings', function (Blueprint $table) {
            $table->foreign(['parent_session_id'])->references(['id'])->on('time_trackings')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_trackings', function (Blueprint $table) {
            $table->dropForeign('time_trackings_parent_session_id_foreign');
        });
    }
};
