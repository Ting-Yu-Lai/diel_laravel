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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();       // VARCHAR(50), 唯一
            $table->string('password_hash', 256);           // VARCHAR(256)
            $table->string('full_name', 100);               // VARCHAR(100)
            $table->string('email', 100);                   // VARCHAR(100)
            $table->text('address')->nullable();            // VARCHAR(100)，可為空
            $table->string('phone', 20)->nullable();        // VARCHAR(100)
            $table->timestamp('last_login_at')->nullable(); // DATETIME，可為空
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
