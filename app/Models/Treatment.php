<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    protected $fillable = ['treatment_category_id', 'name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function treatmentCategory()
    {
        return $this->belongsTo(TreatmentCategory::class);
    }

    public function deleteLogs()
    {
        return $this->hasMany(TreatmentDeleteLog::class);
    }
}
