<?php

namespace App\Repositories;

use App\Models\TreatmentRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TreatmentRecordRepository extends BaseRepository
{
    public function __construct(TreatmentRecord $model)
    {
        parent::__construct($model);
    }

    public function filter(array $params): LengthAwarePaginator
    {
        $query = $this->model->with('customer')
            ->orderByDesc('record_date')
            ->orderByDesc('id');

        if (!empty($params['customer_id'])) {
            $query->where('customer_id', $params['customer_id']);
        }

        if (!empty($params['date_from'])) {
            $query->where('record_date', '>=', $params['date_from']);
        }

        if (!empty($params['date_to'])) {
            $query->where('record_date', '<=', $params['date_to']);
        }

        if (!empty($params['staff_id'])) {
            $query->whereHas('staff', fn($q) => $q->where('staff.id', $params['staff_id']));
        }

        return $query->paginate(15)->withQueryString();
    }
}
