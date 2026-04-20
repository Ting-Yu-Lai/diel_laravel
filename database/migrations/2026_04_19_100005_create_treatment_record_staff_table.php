<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_record_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_record_id')->constrained('treatment_records')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->enum('role', ['doctor', 'nurse', 'consultant']);
            $table->unique(['treatment_record_id', 'staff_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_record_staff');
    }
};
