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
        Schema::create('carousels', function (Blueprint $table) {
            $table->id();
            $table->string('image_url', 255); // 圖片儲存路徑
            $table->string('title', 100)->nullable(); // 圖片標題（選填）
            $table->string('link', 255)->nullable(); // 點擊連結（選填）

            $table->integer('order_num')->default(0)->index(); // 顯示順序，用於排序
            $table->boolean('is_active')->default(1); // 是否啟用
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carousels');
    }
};
