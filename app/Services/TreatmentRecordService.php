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
        private readonly MemberPointsService       $memberPointsService,
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

        $member = $record->load('customer.member')->customer?->member ?? null;
        if ($member) {
            $this->memberPointsService->earnPoints(
                memberId: $member->id,
                points: 50,
                source: 'treatment_record',
                sourceId: $record->id,
                note: null,
            );
        }

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

        $doctorIds    = array_map('intval', $staffByRole['doctor_ids'] ?? []);
        $nurseIds     = array_map('intval', $staffByRole['nurse_ids'] ?? []);
        $consultantId = !empty($staffByRole['consultant_id']) ? (int) $staffByRole['consultant_id'] : null;

        $allStaffIds = array_values(array_unique(array_filter([
            ...$doctorIds,
            ...$nurseIds,
            ...($consultantId ? [$consultantId] : []),
        ])));

        if (empty($allStaffIds)) {
            return;
        }

        // 批次載入 job_title_id，避免 N+1
        $staffJobTitleMap = \App\Models\Staff::whereIn('id', $allStaffIds)
            ->pluck('job_title_id', 'id');

        $syncData = [];
        foreach ([...$doctorIds, ...$nurseIds] as $staffId) {
            if ($jobTitleId = $staffJobTitleMap[$staffId] ?? null) {
                $syncData[$staffId] = ['job_title_id' => $jobTitleId];
            }
        }
        if ($consultantId && ($jobTitleId = $staffJobTitleMap[$consultantId] ?? null)) {
            $syncData[$consultantId] = ['job_title_id' => $jobTitleId];
        }

        $record->staff()->attach($syncData);
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
