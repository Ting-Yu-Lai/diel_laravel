<?php

namespace App\Repositories;

use App\Models\FollowUp;
use Illuminate\Database\Eloquent\Collection;

class FollowUpRepository extends BaseRepository
{
    public function __construct(FollowUp $model)
    {
        parent::__construct($model);
    }

    public function findWithLogs(int $id): ?FollowUp
    {
        return $this->model->with(['logs.photos'])->find($id);
    }

    public function findByItemId(int $itemId): ?FollowUp
    {
        return $this->model->where('treatment_record_item_id', $itemId)->first();
    }

    public function findLatestOngoingByMemberId(int $memberId): ?FollowUp
    {
        return $this->model
            ->where('status', 'ongoing')
            ->whereHas('treatmentRecordItem.treatmentRecord.customer',
                fn($q) => $q->where('member_id', $memberId)
            )
            ->with('treatmentRecordItem.treatmentRecord')
            ->latest('id')
            ->first();
    }

    public function getOngoingWithLineMembers(): Collection
    {
        return $this->model
            ->where('status', 'ongoing')
            ->whereHas('treatmentRecordItem.treatmentRecord.customer.member',
                fn($q) => $q->whereNotNull('line_user_id')
            )
            ->with([
                'treatmentRecordItem.treatment',
                'treatmentRecordItem.treatmentRecord.customer.member',
            ])
            ->get();
    }
}
