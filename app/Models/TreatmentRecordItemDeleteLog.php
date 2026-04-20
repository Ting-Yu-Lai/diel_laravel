<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentRecordItemDeleteLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'treatment_record_item_id',
        'treatment_record_id',
        'treatment_name',
        'deleted_by_admin_id',
        'reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
