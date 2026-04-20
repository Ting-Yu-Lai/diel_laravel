<?php

namespace App\Repositories;

use App\Models\TreatmentRecordItem;
use App\Models\TreatmentRecordItemDeleteLog;

class TreatmentRecordItemRepository extends BaseRepository
{
    public function __construct(TreatmentRecordItem $model)
    {
        parent::__construct($model);
    }

    /** 回傳已載入 treatment 與 staff 關聯的明細（供編輯頁與刪除使用） */
    public function findWithRelations(int $id): ?TreatmentRecordItem
    {
        return $this->model->with(['treatment', 'staff'])->find($id);
    }

    /** 計算指定療程紀錄的明細彙總，供回寫 TreatmentRecord 彙總欄位 */
    public function sumByRecord(int $recordId): array
    {
        $row = $this->model
            ->where('treatment_record_id', $recordId)
            ->selectRaw('COALESCE(SUM(price), 0) as total_amount, COALESCE(SUM(cost), 0) as total_cost, COUNT(*) as item_count')
            ->first();

        return [
            'total_amount' => (int) $row->total_amount,
            'total_cost'   => (int) $row->total_cost,
            'item_count'   => (int) $row->item_count,
        ];
    }

    /** 建立明細刪除日誌 */
    public function createDeleteLog(array $data): void
    {
        TreatmentRecordItemDeleteLog::create($data);
    }
}
