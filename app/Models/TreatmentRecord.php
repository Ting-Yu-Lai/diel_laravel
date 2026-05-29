<?php

namespace App\Models;

use App\Models\Concerns\HasDeleteLogs;
use Illuminate\Database\Eloquent\Model;

class TreatmentRecord extends Model
{
    use HasDeleteLogs;
    protected $fillable = [
        'customer_id',
        'record_date',
        'record_month',
        'total_amount',
        'total_cost',
        'total_profit',
        'is_new_customer',
        'is_return_visit',
        'last_visit_date',
        'item_count',
        'notes',
    ];

    protected $casts = [
        'record_date'      => 'date',
        'last_visit_date'  => 'date',
        'is_new_customer'  => 'boolean',
        'is_return_visit'  => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(TreatmentRecordItem::class);
    }

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'treatment_record_staff')
            ->withPivot('job_title_id');
    }

    public function doctors()
    {
        return $this->belongsToMany(Staff::class, 'treatment_record_staff')
            ->whereHas('jobTitle', fn($q) => $q->where('treatment_role', 'doctor'))
            ->withPivot('job_title_id');
    }

    public function nurses()
    {
        return $this->belongsToMany(Staff::class, 'treatment_record_staff')
            ->whereHas('jobTitle', fn($q) => $q->where('treatment_role', 'nurse'))
            ->withPivot('job_title_id');
    }

    public function consultants()
    {
        return $this->belongsToMany(Staff::class, 'treatment_record_staff')
            ->whereHas('jobTitle', fn($q) => $q->where('treatment_role', 'consultant'))
            ->withPivot('job_title_id');
    }

}
