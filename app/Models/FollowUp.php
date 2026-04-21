<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $fillable = [
        'treatment_record_item_id',
        'status',
        'notes',
    ];

    public function treatmentRecordItem()
    {
        return $this->belongsTo(TreatmentRecordItem::class);
    }

    public function logs()
    {
        return $this->hasMany(FollowUpLog::class)->orderBy('day_number');
    }
}
