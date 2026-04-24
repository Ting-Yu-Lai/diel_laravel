<?php

namespace App\Repositories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Collection;

class MemberRepository extends BaseRepository
{
    public function __construct(Member $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?Member
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?Member
    {
        return $this->model->where('phone', $phone)->first();
    }

    public function findWithCustomer(int $id): ?Member
    {
        return $this->model->with('customer')->find($id);
    }

    public function findByLineUserId(string $lineUserId): ?Member
    {
        return $this->model->where('line_user_id', $lineUserId)->first();
    }

    public function setLineUserId(int $memberId, ?string $lineUserId): void
    {
        $this->model->where('id', $memberId)->update(['line_user_id' => $lineUserId]);
    }

    public function getMembersWithOngoingFollowUp(): Collection
    {
        return $this->model
            ->whereNotNull('line_user_id')
            ->whereHas('customer.treatmentRecords.items.followUp', fn($q) =>
                $q->where('status', 'ongoing')
            )
            ->get();
    }
}
