<?php

namespace App\Repositories;

use App\Models\LinePhotoPending;

class LinePhotoPendingRepository extends BaseRepository
{
    public function __construct(LinePhotoPending $model)
    {
        parent::__construct($model);
    }

    public function upsertCategory(string $lineUserId, string $category): void
    {
        $this->model->updateOrCreate(
            ['line_user_id' => $lineUserId],
            ['category'     => $category]
        );
    }

    public function popCategory(string $lineUserId): ?string
    {
        $record = $this->model->where('line_user_id', $lineUserId)->first();
        if (! $record) return null;
        $category = $record->category;
        $record->delete();
        return $category;
    }
}
