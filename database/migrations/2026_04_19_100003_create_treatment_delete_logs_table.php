<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_delete_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('treatment_id')->nullable();
            $table->string('treatment_name', 100);
            $table->foreignId('deleted_by_admin_id')->constrained('admins');
            $table->text('reason');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_delete_logs');
    }
};
