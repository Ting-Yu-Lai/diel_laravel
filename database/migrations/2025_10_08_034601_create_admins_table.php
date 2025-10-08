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
        Schema::create('admins', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('username', 50)->unique();       //varchar(50), 唯一
            $table->string('password_hash', 256);           //varchar(256)
            $table->string('full_name', 100)->nullable();   //varchar(100), 可唯空
            $table->boolean('power')->default(0)->comment(
                '1=店長, 0=員工, 店長管理者資料表');          // TINYINT(1)，預設為 0
            $table->string('email', 100)->nullable();       // VARCHAR(100)，可為空
            $table->timestamps();
            $table->timestamp('last_login_at')->nullable(); // DATETIME，可為空
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
