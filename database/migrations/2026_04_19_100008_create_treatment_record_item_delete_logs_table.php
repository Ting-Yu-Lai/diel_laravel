<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_record_item_delete_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('treatment_record_item_id');
            $table->unsignedBigInteger('treatment_record_id');
            $table->string('treatment_name', 100);
            $table->foreignId('deleted_by_admin_id')->constrained('admins');
            $table->string('reason', 500);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_record_item_delete_logs');
    }
};
