<?php

namespace App\Services;

use App\Models\FollowUpLog;
use App\Repositories\FollowUpLogRepository;

class FollowUpLogService
{
    public function __construct(
        private readonly FollowUpLogRepository $repo,
    ) {}

    public function create(int $followUpId, array $data): FollowUpLog
    {
        $data['follow_up_id'] = $followUpId;

        return $this->repo->create($data);
    }

    public function find(int $id): ?FollowUpLog
    {
        return $this->repo->find($id);
    }

    public function findWithPhotos(int $id): ?FollowUpLog
    {
        return $this->repo->findWithPhotos($id);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id, string $reason, int $adminId): void
    {
        $log = $this->repo->find($id);

        $this->repo->createDeleteLog([
            'follow_up_log_id'    => $log->id,
            'follow_up_id'        => $log->follow_up_id,
            'deleted_by_admin_id' => $adminId,
            'reason'              => $reason,
        ]);

        $this->repo->delete($id);
    }
}
