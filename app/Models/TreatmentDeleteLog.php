<?php

namespace App\Models;

use App\Models\Concerns\IsDeleteLog;
use Illuminate\Database\Eloquent\Model;

class TreatmentDeleteLog extends Model
{
    use IsDeleteLog;

    protected $fillable = [
        'treatment_id',
        'treatment_name',
        'deleted_by_admin_id',
        'reason',
    ];
}
