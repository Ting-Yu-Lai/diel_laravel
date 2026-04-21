<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_up_log_delete_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('follow_up_log_id');
            $table->unsignedBigInteger('follow_up_id');
            $table->foreignId('deleted_by_admin_id')->constrained('admins');
            $table->string('reason', 500);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_up_log_delete_logs');
    }
};
