<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. 職稱 ─────────────────────────────────────
        $this->insertAll('job_titles', [
            ['name' => '院長醫師'],
            ['name' => '主治醫師'],
            ['name' => '護理師'],
            ['name' => '醫美顧問'],
        ]);
        $jt = DB::table('job_titles')->pluck('id', 'name');

        // ── 2. 工作人員 ──────────────────────────────────
        $this->insertAll('staff', [
            ['job_title_id' => $jt['院長醫師'], 'name' => '林建宏', 'gender' => 'M', 'phone' => '0912001001', 'email' => 'lin@diel.com',   'hire_date' => '2020-01-01', 'is_active' => true],
            ['job_title_id' => $jt['主治醫師'], 'name' => '陳雅婷', 'gender' => 'F', 'phone' => '0912001002', 'email' => 'chen@diel.com',  'hire_date' => '2021-03-15', 'is_active' => true],
            ['job_title_id' => $jt['護理師'],   'name' => '王思穎', 'gender' => 'F', 'phone' => '0912001003', 'email' => 'wang@diel.com',  'hire_date' => '2022-06-01', 'is_active' => true],
            ['job_title_id' => $jt['醫美顧問'], 'name' => '張雅琪', 'gender' => 'F', 'phone' => '0912001004', 'email' => 'zhang@diel.com', 'hire_date' => '2023-01-10', 'is_active' => true],
        ]);
        $staff = DB::table('staff')->pluck('id', 'name');

        // ── 3. 標籤分類 + 標籤 ──────────────────────────
        $this->insertAll('tag_categories', [
            ['name' => '膚質分類'],
            ['name' => '客戶屬性'],
            ['name' => '關注部位'],
        ]);
        $tc = DB::table('tag_categories')->pluck('id', 'name');

        $this->insertAll('tags', [
            ['tag_category_id' => $tc['膚質分類'], 'name' => '油性肌'],
            ['tag_category_id' => $tc['膚質分類'], 'name' => '乾性肌'],
            ['tag_category_id' => $tc['膚質分類'], 'name' => '混合肌'],
            ['tag_category_id' => $tc['膚質分類'], 'name' => '敏感肌'],
            ['tag_category_id' => $tc['客戶屬性'], 'name' => 'VIP客戶'],
            ['tag_category_id' => $tc['客戶屬性'], 'name' => '老客戶'],
            ['tag_category_id' => $tc['客戶屬性'], 'name' => '轉介紹新客'],
            ['tag_category_id' => $tc['關注部位'], 'name' => '全臉'],
            ['tag_category_id' => $tc['關注部位'], 'name' => '眼周'],
            ['tag_category_id' => $tc['關注部位'], 'name' => 'T字部位'],
        ]);
        $tags = DB::table('tags')->pluck('id', 'name');

        // ── 4. 療程分類 + 療程 ──────────────────────────
        $this->insertAll('treatment_categories', [
            ['name' => '注射療程'],
            ['name' => '雷射光療'],
            ['name' => '臉部護理'],
        ]);
        $catIds = DB::table('treatment_categories')->pluck('id', 'name');

        $this->insertAll('treatments', [
            ['treatment_category_id' => $catIds['注射療程'], 'name' => '玻尿酸填充',   'is_active' => true],
            ['treatment_category_id' => $catIds['注射療程'], 'name' => '肉毒桿菌注射', 'is_active' => true],
            ['treatment_category_id' => $catIds['注射療程'], 'name' => '童顏針',       'is_active' => true],
            ['treatment_category_id' => $catIds['注射療程'], 'name' => '液態拉皮',     'is_active' => true],
            ['treatment_category_id' => $catIds['雷射光療'], 'name' => '皮秒雷射',     'is_active' => true],
            ['treatment_category_id' => $catIds['雷射光療'], 'name' => '飛梭雷射',     'is_active' => true],
            ['treatment_category_id' => $catIds['雷射光療'], 'name' => '淨膚雷射',     'is_active' => true],
            ['treatment_category_id' => $catIds['雷射光療'], 'name' => '光子嫩膚',     'is_active' => true],
            ['treatment_category_id' => $catIds['臉部護理'], 'name' => '水氧肌活護理', 'is_active' => true],
            ['treatment_category_id' => $catIds['臉部護理'], 'name' => '杏仁酸煥膚',   'is_active' => true],
            ['treatment_category_id' => $catIds['臉部護理'], 'name' => 'HIFU超音波拉提', 'is_active' => true],
        ]);
        $treat = DB::table('treatments')->pluck('id', 'name');

        // ── 5. 會員帳號 ─────────────────────────────────
        $this->insertAll('members', [
            ['email' => 'emily@demo.com', 'password_hash' => Hash::make('1234'), 'full_name' => '陳美玲', 'phone' => '0912100001'],
            ['email' => 'yuhan@demo.com', 'password_hash' => Hash::make('1234'), 'full_name' => '李雨涵', 'phone' => '0912100002'],
            ['email' => 'wei@demo.com',   'password_hash' => Hash::make('1234'), 'full_name' => '王志偉', 'phone' => '0912100003'],
        ]);
        $memberIds = DB::table('members')->pluck('id', 'email');

        // ── 6. 客戶 ─────────────────────────────────────
        $this->insertAll('customers', [
            ['name' => '陳美玲', 'gender' => 'F', 'birth_date' => '1990-03-15', 'phone' => '0912100001', 'email' => 'emily@demo.com', 'blood_type' => 'A',      'source' => 'online',   'is_active' => true, 'member_id' => $memberIds['emily@demo.com'], 'occupation' => '設計師',   'allergies' => null, 'medical_history' => null],
            ['name' => '李雨涵', 'gender' => 'F', 'birth_date' => '1988-07-22', 'phone' => '0912100002', 'email' => 'yuhan@demo.com', 'blood_type' => 'B',      'source' => 'referral', 'is_active' => true, 'member_id' => $memberIds['yuhan@demo.com'], 'occupation' => '教師',     'allergies' => null, 'medical_history' => null],
            ['name' => '王志偉', 'gender' => 'M', 'birth_date' => '1992-11-08', 'phone' => '0912100003', 'email' => 'wei@demo.com',   'blood_type' => 'O',      'source' => 'walk_in',  'is_active' => true, 'member_id' => $memberIds['wei@demo.com'],   'occupation' => '工程師',   'allergies' => null, 'medical_history' => null],
            ['name' => '張婉君', 'gender' => 'F', 'birth_date' => '1985-05-30', 'phone' => '0912200001', 'email' => null,             'blood_type' => 'AB',     'source' => 'referral', 'is_active' => true, 'member_id' => null,                'occupation' => '自由業',   'allergies' => '花粉過敏', 'medical_history' => null],
            ['name' => '林佳蓉', 'gender' => 'F', 'birth_date' => '1995-01-18', 'phone' => '0912200002', 'email' => null,             'blood_type' => 'A',      'source' => 'online',   'is_active' => true, 'member_id' => null,                'occupation' => '業務員',   'allergies' => null, 'medical_history' => null],
            ['name' => '吳明哲', 'gender' => 'M', 'birth_date' => '1983-09-12', 'phone' => '0912300001', 'email' => null,             'blood_type' => 'unknown', 'source' => 'walk_in', 'is_active' => true, 'member_id' => null,                'occupation' => '企業主',   'allergies' => null, 'medical_history' => '高血壓（控制中）'],
        ]);
        $cust = DB::table('customers')->pluck('id', 'name');

        // 客戶標籤
        DB::table('customer_tag')->insert([
            ['customer_id' => $cust['陳美玲'], 'tag_id' => $tags['VIP客戶']],
            ['customer_id' => $cust['陳美玲'], 'tag_id' => $tags['混合肌']],
            ['customer_id' => $cust['陳美玲'], 'tag_id' => $tags['全臉']],
            ['customer_id' => $cust['李雨涵'], 'tag_id' => $tags['老客戶']],
            ['customer_id' => $cust['李雨涵'], 'tag_id' => $tags['乾性肌']],
            ['customer_id' => $cust['李雨涵'], 'tag_id' => $tags['全臉']],
            ['customer_id' => $cust['王志偉'], 'tag_id' => $tags['油性肌']],
            ['customer_id' => $cust['王志偉'], 'tag_id' => $tags['T字部位']],
            ['customer_id' => $cust['張婉君'], 'tag_id' => $tags['轉介紹新客']],
            ['customer_id' => $cust['張婉君'], 'tag_id' => $tags['敏感肌']],
            ['customer_id' => $cust['林佳蓉'], 'tag_id' => $tags['VIP客戶']],
            ['customer_id' => $cust['林佳蓉'], 'tag_id' => $tags['眼周']],
            ['customer_id' => $cust['吳明哲'], 'tag_id' => $tags['VIP客戶']],
            ['customer_id' => $cust['吳明哲'], 'tag_id' => $tags['T字部位']],
        ]);

        // ── 7. 療程紀錄 ─────────────────────────────────

        // ① 陳美玲｜皮秒雷射｜2026-04-20｜追蹤進行中（5天恢復期）
        $this->makeRecord(
            custId: $cust['陳美玲'],
            date: '2026-04-20',
            amount: 12000, cost: 4000,
            isNew: false, isReturn: true,
            notes: '客戶反映希望改善毛孔粗大與色斑，使用皮秒雷射全臉治療。',
            doctorId: $staff['陳雅婷'],
            item: ['treatment_id' => $treat['皮秒雷射'], 'body_part' => '全臉', 'dose' => '全臉一個療程', 'price' => 12000, 'cost' => 4000, 'staff_id' => $staff['陳雅婷']],
            followUp: [
                'status' => 'ongoing',
                'notes'  => '請每日回傳恢復狀況，若有紅腫熱痛請立即聯繫診所。',
                'before' => ['https://picsum.photos/seed/emily_before/400/500'],
                'after'  => [],
                'days'   => [1, 2, 3, 5, 7],
                'photoSeed' => 'emily',
                'createdAt' => '2026-04-20',
            ]
        );

        // ② 李雨涵｜玻尿酸填充｜2026-03-10｜追蹤已完成（有術前術後對比）
        $this->makeRecord(
            custId: $cust['李雨涵'],
            date: '2026-03-10',
            amount: 18000, cost: 6000,
            isNew: false, isReturn: true,
            notes: '法令紋填充，使用瑞典廠牌玻尿酸 1cc，效果自然。',
            doctorId: $staff['林建宏'],
            item: ['treatment_id' => $treat['玻尿酸填充'], 'body_part' => '法令紋', 'dose' => '1cc', 'price' => 18000, 'cost' => 6000, 'staff_id' => $staff['林建宏']],
            followUp: [
                'status' => 'completed',
                'notes'  => '追蹤完成，效果良好，客戶滿意度高。建議半年後回診補打。',
                'before' => ['https://picsum.photos/seed/yuhan_before/400/500'],
                'after'  => ['https://picsum.photos/seed/yuhan_after/400/500'],
                'days'   => [1, 3, 7],
                'photoSeed' => 'yuhan',
                'createdAt' => '2026-03-10',
            ]
        );

        // ③ 王志偉｜肉毒桿菌｜2026-05-01｜追蹤進行中（2天）
        $this->makeRecord(
            custId: $cust['王志偉'],
            date: '2026-05-01',
            amount: 8000, cost: 2500,
            isNew: true, isReturn: false,
            notes: '新客戶，主訴額頭紋路深，施打肉毒 20U。術後提醒 4 小時內勿低頭。',
            doctorId: $staff['林建宏'],
            item: ['treatment_id' => $treat['肉毒桿菌注射'], 'body_part' => '額頭', 'dose' => '20U', 'price' => 8000, 'cost' => 2500, 'staff_id' => $staff['林建宏']],
            followUp: [
                'status' => 'ongoing',
                'notes'  => null,
                'before' => ['https://picsum.photos/seed/wei_before/400/500'],
                'after'  => [],
                'days'   => [1, 2],
                'photoSeed' => 'wei',
                'createdAt' => '2026-05-01',
            ]
        );

        // ④ 張婉君｜光子嫩膚 + 杏仁酸｜2026-04-10｜純療程紀錄（無追蹤）
        $date4 = Carbon::parse('2026-04-10');
        $rec4  = DB::table('treatment_records')->insertGetId([
            'customer_id'    => $cust['張婉君'],
            'record_date'    => $date4->toDateString(),
            'record_month'   => $date4->format('Y-m'),
            'total_amount'   => 6000,
            'total_cost'     => 1800,
            'total_profit'   => 4200,
            'is_new_customer'=> false,
            'is_return_visit'=> true,
            'item_count'     => 2,
            'notes'          => null,
            'created_at'     => $date4,
            'updated_at'     => $date4,
        ]);
        DB::table('treatment_record_staff')->insert(['treatment_record_id' => $rec4, 'staff_id' => $staff['陳雅婷'], 'role' => 'doctor']);
        DB::table('treatment_record_items')->insert([
            ['treatment_record_id' => $rec4, 'treatment_id' => $treat['光子嫩膚'],   'body_part' => '全臉', 'dose' => null, 'price' => 4500, 'cost' => 1200, 'staff_id' => $staff['陳雅婷'], 'notes' => null, 'created_at' => $date4, 'updated_at' => $date4],
            ['treatment_record_id' => $rec4, 'treatment_id' => $treat['杏仁酸煥膚'], 'body_part' => '全臉', 'dose' => null, 'price' => 1500, 'cost' =>  600, 'staff_id' => $staff['王思穎'], 'notes' => null, 'created_at' => $date4, 'updated_at' => $date4],
        ]);

        // ⑤ 林佳蓉｜HIFU｜2026-02-15｜追蹤已完成
        $this->makeRecord(
            custId: $cust['林佳蓉'],
            date: '2026-02-15',
            amount: 25000, cost: 8000,
            isNew: false, isReturn: false,
            notes: '全臉 HIFU 超音波拉提，共 600 發，療程順利。',
            doctorId: $staff['林建宏'],
            item: ['treatment_id' => $treat['HIFU超音波拉提'], 'body_part' => '全臉', 'dose' => '600發', 'price' => 25000, 'cost' => 8000, 'staff_id' => $staff['林建宏']],
            followUp: [
                'status' => 'completed',
                'notes'  => '效果顯著，客戶對拉提效果非常滿意，建議 12 個月後再次療程。',
                'before' => ['https://picsum.photos/seed/lin_before/400/500'],
                'after'  => ['https://picsum.photos/seed/lin_after/400/500'],
                'days'   => [3, 7, 14],
                'photoSeed' => 'lin',
                'createdAt' => '2026-02-15',
            ]
        );

        // ⑥ 吳明哲｜肉毒 + 童顏針｜2026-01-08｜純療程紀錄
        $date6 = Carbon::parse('2026-01-08');
        $rec6  = DB::table('treatment_records')->insertGetId([
            'customer_id'    => $cust['吳明哲'],
            'record_date'    => $date6->toDateString(),
            'record_month'   => $date6->format('Y-m'),
            'total_amount'   => 38000,
            'total_cost'     => 13000,
            'total_profit'   => 25000,
            'is_new_customer'=> false,
            'is_return_visit'=> true,
            'item_count'     => 2,
            'notes'          => '高血壓控制中，術前確認血壓正常後施打。',
            'created_at'     => $date6,
            'updated_at'     => $date6,
        ]);
        DB::table('treatment_record_staff')->insert(['treatment_record_id' => $rec6, 'staff_id' => $staff['林建宏'], 'role' => 'doctor']);
        DB::table('treatment_record_items')->insert([
            ['treatment_record_id' => $rec6, 'treatment_id' => $treat['肉毒桿菌注射'], 'body_part' => '眼周', 'dose' => '15U', 'price' => 8000,  'cost' => 2500,  'staff_id' => $staff['林建宏'], 'notes' => null, 'created_at' => $date6, 'updated_at' => $date6],
            ['treatment_record_id' => $rec6, 'treatment_id' => $treat['童顏針'],       'body_part' => '全臉', 'dose' => '1瓶', 'price' => 30000, 'cost' => 10500, 'staff_id' => $staff['林建宏'], 'notes' => null, 'created_at' => $date6, 'updated_at' => $date6],
        ]);
    }

    // ── 建立療程紀錄 + 項目 + 追蹤 的便利方法 ──────────
    private function makeRecord(
        int $custId, string $date,
        int $amount, int $cost,
        bool $isNew, bool $isReturn,
        ?string $notes,
        int $doctorId,
        array $item,
        array $followUp,
    ): void {
        $dt = Carbon::parse($date);

        $recId = DB::table('treatment_records')->insertGetId([
            'customer_id'    => $custId,
            'record_date'    => $dt->toDateString(),
            'record_month'   => $dt->format('Y-m'),
            'total_amount'   => $amount,
            'total_cost'     => $cost,
            'total_profit'   => $amount - $cost,
            'is_new_customer'=> $isNew,
            'is_return_visit'=> $isReturn,
            'item_count'     => 1,
            'notes'          => $notes,
            'created_at'     => $dt,
            'updated_at'     => $dt,
        ]);

        DB::table('treatment_record_staff')->insert([
            'treatment_record_id' => $recId,
            'staff_id'            => $doctorId,
            'role'                => 'doctor',
        ]);

        $itemId = DB::table('treatment_record_items')->insertGetId([
            'treatment_record_id' => $recId,
            'treatment_id'        => $item['treatment_id'],
            'body_part'           => $item['body_part'] ?? null,
            'dose'                => $item['dose'] ?? null,
            'price'               => $item['price'],
            'cost'                => $item['cost'],
            'staff_id'            => $item['staff_id'],
            'notes'               => null,
            'created_at'          => $dt,
            'updated_at'          => $dt,
        ]);

        $fuCreated = Carbon::parse($followUp['createdAt']);
        $fuId = DB::table('follow_ups')->insertGetId([
            'treatment_record_item_id' => $itemId,
            'status'                   => $followUp['status'],
            'notes'                    => $followUp['notes'],
            'created_at'               => $fuCreated,
            'updated_at'               => now(),
        ]);

        // 術前照片
        foreach ($followUp['before'] as $url) {
            DB::table('follow_up_photos')->insert([
                'follow_up_id'     => $fuId,
                'follow_up_log_id' => null,
                'photo_url'        => $url,
                'category'         => 'before',
                'created_at'       => $fuCreated,
                'updated_at'       => $fuCreated,
            ]);
        }

        // 術後照片
        foreach ($followUp['after'] as $url) {
            $afterDt = $fuCreated->copy()->addDays(30);
            DB::table('follow_up_photos')->insert([
                'follow_up_id'     => $fuId,
                'follow_up_log_id' => null,
                'photo_url'        => $url,
                'category'         => 'after',
                'created_at'       => $afterDt,
                'updated_at'       => $afterDt,
            ]);
        }

        // 恢復期時間軸
        foreach ($followUp['days'] as $day) {
            $logDt = $fuCreated->copy()->addDays($day);
            $logId = DB::table('follow_up_logs')->insertGetId([
                'follow_up_id' => $fuId,
                'day_number'   => $day,
                'content'      => null,
                'created_at'   => $logDt,
                'updated_at'   => $logDt,
            ]);
            DB::table('follow_up_photos')->insert([
                'follow_up_id'     => null,
                'follow_up_log_id' => $logId,
                'photo_url'        => "https://picsum.photos/seed/{$followUp['photoSeed']}d{$day}/400/500",
                'category'         => 'recovery',
                'created_at'       => $logDt,
                'updated_at'       => $logDt,
            ]);
        }
    }

    // ── 通用：批次 insert 並加上 timestamps ──────────────
    private function insertAll(string $table, array $rows): void
    {
        DB::table($table)->insert(
            array_map(fn($r) => [...$r, 'created_at' => now(), 'updated_at' => now()], $rows)
        );
    }
}
