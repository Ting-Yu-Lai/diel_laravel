<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentRecordItem extends Model
{
    protected $fillable = [
        'treatment_record_id',
        'treatment_id',
        'body_part',
        'dose',
        'price',
        'cost',
        'staff_id',
        'notes',
    ];

    public function treatmentRecord()
    {
        return $this->belongsTo(TreatmentRecord::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
