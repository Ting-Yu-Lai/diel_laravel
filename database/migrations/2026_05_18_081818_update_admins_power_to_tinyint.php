<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->tinyInteger('power')->default(0)->change();
        });

        // 現有 power=1 帳號升為超級管理員 (2)
        DB::table('admins')->where('power', 1)->update(['power' => 2]);
    }

    public function down(): void
    {
        DB::table('admins')->where('power', 2)->update(['power' => 1]);
        DB::table('admins')->where('power', 1)->update(['power' => 0]);

        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('power')->default(0)->change();
        });
    }
};
