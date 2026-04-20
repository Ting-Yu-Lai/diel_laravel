<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_record_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_record_id')->constrained('treatment_records')->cascadeOnDelete();
            $table->foreignId('treatment_id')->constrained('treatments');
            $table->string('body_part', 100)->nullable();
            $table->string('dose', 100)->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('cost')->default(0);
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_record_items');
    }
};
