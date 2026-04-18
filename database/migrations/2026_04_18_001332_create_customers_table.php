<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('id');

            // ---------- 基本資料 ----------
            $table->string('name', 50)->comment('姓名');
            $table->enum('gender', ['M', 'F', 'other'])->nullable()->comment('性別');
            $table->date('birth_date')->nullable()->comment('出生日期');
            $table->string('phone', 20)->unique()->comment('手機（唯一）');
            $table->string('email', 100)->nullable()->comment('電子郵件');
            $table->string('id_number', 20)->nullable()->unique()->comment('身分證字號');
            $table->string('address', 200)->nullable()->comment('地址');
            $table->string('occupation', 50)->nullable()->comment('職業');

            // ---------- 緊急聯絡人 ----------
            $table->string('emergency_contact', 50)->nullable()->comment('緊急聯絡人姓名');
            $table->string('emergency_phone', 20)->nullable()->comment('緊急聯絡人電話');

            // ---------- 醫療資訊 ----------
            $table->enum('blood_type', ['A', 'B', 'AB', 'O', 'unknown'])
                ->default('unknown')->comment('血型');
            $table->text('allergies')->nullable()->comment('過敏史');
            $table->text('medical_history')->nullable()->comment('病史');

            // ---------- 其他 ----------
            $table->enum('source', ['walk_in', 'referral', 'online', 'other'])
                ->default('walk_in')->comment('來源');
            $table->text('notes')->nullable()->comment('備註');
            $table->boolean('is_active')->default(1)->comment('是否啟用');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
