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
        if (Schema::hasColumn('treatments', 'price')) {
            Schema::table('treatments', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
    }

    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->unsignedInteger('price')->after('name');
        });
    }
};
