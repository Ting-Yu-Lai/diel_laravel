<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. 加入 job_title_id（nullable，供資料遷移用）
        Schema::table('treatment_record_staff', function (Blueprint $table) {
            $table->foreignId('job_title_id')
                  ->nullable()
                  ->after('staff_id')
                  ->constrained('job_titles')
                  ->restrictOnDelete();
        });

        // 2. 資料遷移：從 staff 的當前 job_title_id 填入 pivot
        DB::statement("
            UPDATE treatment_record_staff
            SET job_title_id = (
                SELECT job_title_id FROM staff
                WHERE staff.id = treatment_record_staff.staff_id
            )
            WHERE EXISTS (
                SELECT 1 FROM staff
                WHERE staff.id = treatment_record_staff.staff_id
                AND staff.job_title_id IS NOT NULL
            )
        ");

        // 3. 設為 NOT NULL
        Schema::table('treatment_record_staff', function (Blueprint $table) {
            $table->unsignedBigInteger('job_title_id')->nullable(false)->change();
        });

        // 4. 先建立新 unique constraint（讓 MySQL 有新 index 支撐 FK，才能刪舊 index）
        Schema::table('treatment_record_staff', function (Blueprint $table) {
            $table->unique(['treatment_record_id', 'staff_id', 'job_title_id'], 'trs_record_staff_jt_unique');
        });

        // 5. 再移除舊 unique constraint 與 role 欄位
        Schema::table('treatment_record_staff', function (Blueprint $table) {
            $table->dropUnique('treatment_record_staff_treatment_record_id_staff_id_role_unique');
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('treatment_record_staff', function (Blueprint $table) {
            $table->dropUnique('trs_record_staff_jt_unique');
        });

        Schema::table('treatment_record_staff', function (Blueprint $table) {
            $table->enum('role', ['doctor', 'nurse', 'consultant'])->default('doctor')->after('staff_id');
        });

        DB::statement("
            UPDATE treatment_record_staff
            SET role = (
                SELECT treatment_role FROM job_titles
                WHERE job_titles.id = treatment_record_staff.job_title_id
            )
            WHERE EXISTS (
                SELECT 1 FROM job_titles
                WHERE job_titles.id = treatment_record_staff.job_title_id
                AND job_titles.treatment_role IS NOT NULL
            )
        ");

        Schema::table('treatment_record_staff', function (Blueprint $table) {
            $table->dropForeign(['job_title_id']);
            $table->dropColumn('job_title_id');
        });

        Schema::table('treatment_record_staff', function (Blueprint $table) {
            $table->unique(['treatment_record_id', 'staff_id', 'role']);
        });
    }
};
