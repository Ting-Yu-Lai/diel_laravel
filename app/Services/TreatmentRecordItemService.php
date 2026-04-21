<?php

namespace App\Services;

use App\Models\TreatmentRecordItem;
use App\Repositories\TreatmentRecordItemRepository;
use App\Repositories\TreatmentRecordRepository;
use App\Services\FollowUpService;

class TreatmentRecordItemService
{
    public function __construct(
        private readonly TreatmentRecordItemRepository $itemRepo,
        private readonly TreatmentRecordRepository     $recordRepo,
        private readonly FollowUpService               $followUpService,
    ) {}

    public function findById(int $id): ?TreatmentRecordItem
    {
        return $this->itemRepo->findWithRelations($id);
    }

    public function create(int $recordId, array $data): TreatmentRecordItem
    {
        $data['treatment_record_id'] = $recordId;
        $item = $this->itemRepo->create($data);
        $this->followUpService->createForItem($item->id);
        $this->syncRecordTotals($recordId);

        return $item;
    }

    public function update(int $id, array $data): TreatmentRecordItem
    {
        $this->itemRepo->update($id, $data);
        $item = $this->itemRepo->find($id);
        $this->syncRecordTotals($item->treatment_record_id);

        return $item;
    }

    /**
     * 刪除明細並寫入刪除日誌
     *
     * @param string $treatmentName 由 Controller 提前載入，避免 Service 直接存取 ORM 關聯
     */
    public function delete(int $id, string $treatmentName, string $reason, int $adminId): void
    {
        $item     = $this->itemRepo->find($id);
        $recordId = $item->treatment_record_id;

        $this->itemRepo->createDeleteLog([
            'treatment_record_item_id' => $item->id,
            'treatment_record_id'      => $recordId,
            'treatment_name'           => $treatmentName,
            'deleted_by_admin_id'      => $adminId,
            'reason'                   => $reason,
        ]);

        $this->itemRepo->delete($id);
        $this->syncRecordTotals($recordId);
    }

    private function syncRecordTotals(int $recordId): void
    {
        $totals = $this->itemRepo->sumByRecord($recordId);

        $this->recordRepo->update($recordId, [
            'total_amount'  => $totals['total_amount'],
            'total_cost'    => $totals['total_cost'],
            'total_profit'  => $totals['total_amount'] - $totals['total_cost'],
            'item_count'    => $totals['item_count'],
        ]);
    }
}
