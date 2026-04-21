<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_record_item_id')
                  ->unique()
                  ->constrained('treatment_record_items')
                  ->cascadeOnDelete();
            $table->enum('status', ['ongoing', 'completed', 'abnormal'])->default('ongoing');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
