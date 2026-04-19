<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_title_id',
        'name',
        'gender',
        'phone',
        'email',
        'hire_date',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function deleteLogs()
    {
        return $this->hasMany(StaffDeleteLog::class);
    }
}
