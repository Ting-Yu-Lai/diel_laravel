<?php

namespace App\Services;

use App\Models\FollowUp;
use App\Repositories\FollowUpRepository;

class FollowUpService
{
    public function __construct(
        private readonly FollowUpRepository $repo,
    ) {}

    public function createForItem(int $itemId): FollowUp
    {
        return $this->repo->create([
            'treatment_record_item_id' => $itemId,
            'status'                   => 'ongoing',
        ]);
    }

    public function findWithLogs(int $id): ?FollowUp
    {
        return $this->repo->findWithLogs($id);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }
}
