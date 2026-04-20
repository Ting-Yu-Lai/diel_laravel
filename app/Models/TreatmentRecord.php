<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentRecord extends Model
{
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
            ->withPivot('role');
    }

    public function doctors()
    {
        return $this->belongsToMany(Staff::class, 'treatment_record_staff')
            ->wherePivot('role', 'doctor');
    }

    public function nurses()
    {
        return $this->belongsToMany(Staff::class, 'treatment_record_staff')
            ->wherePivot('role', 'nurse');
    }

    public function consultants()
    {
        return $this->belongsToMany(Staff::class, 'treatment_record_staff')
            ->wherePivot('role', 'consultant');
    }

    public function deleteLogs()
    {
        return $this->hasMany(TreatmentRecordDeleteLog::class);
    }
}
