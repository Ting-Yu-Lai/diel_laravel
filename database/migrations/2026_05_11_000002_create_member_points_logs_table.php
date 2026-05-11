<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_points_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('type', 20)->comment('earn / redeem / adjust');
            $table->integer('points')->comment('正數加點，負數扣點');
            $table->unsignedInteger('balance_after')->comment('操作後餘額快照');
            $table->string('source', 50)->comment('treatment_record / manual / redemption');
            $table->unsignedBigInteger('source_id')->nullable()->comment('關聯來源記錄 ID');
            $table->string('note', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_points_logs');
    }
};
