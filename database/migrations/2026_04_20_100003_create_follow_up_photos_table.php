<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_up_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follow_up_log_id')
                  ->constrained('follow_up_logs')
                  ->cascadeOnDelete();
            $table->string('photo_url', 500);
            $table->enum('category', ['before', 'after', 'recovery']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_up_photos');
    }
};
