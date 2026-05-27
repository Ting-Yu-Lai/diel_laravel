<?php

namespace App\Models;

use App\Models\Concerns\IsDeleteLog;
use Illuminate\Database\Eloquent\Model;

class TreatmentRecordDeleteLog extends Model
{
    use IsDeleteLog;

    protected $fillable = [
        'treatment_record_id',
        'customer_id',
        'record_date',
        'deleted_by_admin_id',
        'reason',
    ];

    protected $casts = [
        'record_date' => 'date',
    ];
}
