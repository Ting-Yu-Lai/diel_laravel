<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->date('record_date');
            $table->char('record_month', 7)->index();
            $table->unsignedInteger('total_amount')->default(0);
            $table->unsignedInteger('total_cost')->default(0);
            $table->integer('total_profit')->default(0);
            $table->boolean('is_new_customer')->default(false);
            $table->boolean('is_return_visit')->default(false);
            $table->date('last_visit_date')->nullable();
            $table->unsignedSmallInteger('item_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_records');
    }
};
