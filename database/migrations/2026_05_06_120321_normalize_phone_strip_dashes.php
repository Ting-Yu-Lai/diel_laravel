<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE members   SET phone           = REPLACE(phone, '-', '') WHERE phone           LIKE '%-%'");
        DB::statement("UPDATE customers SET phone           = REPLACE(phone, '-', '') WHERE phone           LIKE '%-%'");
        DB::statement("UPDATE customers SET emergency_phone = REPLACE(emergency_phone, '-', '') WHERE emergency_phone LIKE '%-%'");
    }

    public function down(): void
    {
        // intentionally irreversible — raw digits are the canonical format
    }
};
