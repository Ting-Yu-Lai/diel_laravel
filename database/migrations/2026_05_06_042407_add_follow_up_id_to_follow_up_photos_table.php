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
        Schema::table('follow_up_photos', function (Blueprint $table) {
            $table->dropForeign(['follow_up_log_id']);
            $table->unsignedBigInteger('follow_up_log_id')->nullable()->change();
            $table->foreign('follow_up_log_id')->references('id')->on('follow_up_logs')->cascadeOnDelete();
            $table->foreignId('follow_up_id')->nullable()->after('id')->constrained('follow_ups')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('follow_up_photos', function (Blueprint $table) {
            $table->dropForeign(['follow_up_id']);
            $table->dropColumn('follow_up_id');
            $table->dropForeign(['follow_up_log_id']);
            $table->unsignedBigInteger('follow_up_log_id')->nullable(false)->change();
            $table->foreign('follow_up_log_id')->references('id')->on('follow_up_logs')->cascadeOnDelete();
        });
    }
};
