<?php

namespace App\Services;

use App\Models\TreatmentRecord;
use App\Repositories\TreatmentRecordRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TreatmentRecordService
{
    public function __construct(
        private readonly TreatmentRecordRepository $repo,
    ) {}

    public function filter(array $params): LengthAwarePaginator
    {
        return $this->repo->filter($params);
    }

    public function findById(int $id): ?TreatmentRecord
    {
        return $this->repo->find($id);
    }

    public function create(array $data, array $staffByRole): TreatmentRecord
    {
        $data = $this->appendAutoFields($data);

        $record = $this->repo->create($data);

        $this->syncStaff($record, $staffByRole);

        return $record;
    }

    public function update(int $id, array $data, array $staffByRole): bool
    {
        $data = $this->appendAutoFields($data, $id);

        $result = $this->repo->update($id, $data);

        $record = $this->repo->find($id);
        $this->syncStaff($record, $staffByRole);

        return $result;
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function syncStaff(TreatmentRecord $record, array $staffByRole): void
    {
        $record->staff()->detach();

        // 醫師、護理師：多選（陣列）
        foreach (['doctor' => 'doctor_ids', 'nurse' => 'nurse_ids'] as $role => $key) {
            foreach ($staffByRole[$key] ?? [] as $staffId) {
                $record->staff()->attach((int) $staffId, ['role' => $role]);
            }
        }

        // 諮詢師：單選（單一 ID）
        if (!empty($staffByRole['consultant_id'])) {
            $record->staff()->attach((int) $staffByRole['consultant_id'], ['role' => 'consultant']);
        }
    }

    private function appendAutoFields(array $data, ?int $excludeId = null): array
    {
        $recordDate = Carbon::parse($data['record_date']);

        $data['record_month'] = $recordDate->format('Y-m');
        $data['total_profit'] = ($data['total_amount'] ?? 0) - ($data['total_cost'] ?? 0);

        $customerId = $data['customer_id'];

        $priorRecord = TreatmentRecord::where('customer_id', $customerId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->orderByDesc('record_date')
            ->first();

        $data['is_new_customer'] = $priorRecord === null;
        $data['is_return_visit'] = $priorRecord !== null;
        $data['last_visit_date'] = $priorRecord?->record_date?->toDateString();

        return $data;
    }
}
