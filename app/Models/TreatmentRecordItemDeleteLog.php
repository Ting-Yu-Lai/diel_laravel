<?php

namespace App\Models;

use App\Models\Concerns\IsDeleteLog;
use Illuminate\Database\Eloquent\Model;

class TreatmentRecordItemDeleteLog extends Model
{
    use IsDeleteLog;

    protected $fillable = [
        'treatment_record_item_id',
        'treatment_record_id',
        'treatment_name',
        'deleted_by_admin_id',
        'reason',
    ];
}
