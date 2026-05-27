<?php

namespace App\Models;

use App\Models\Concerns\HasDeleteLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes, HasDeleteLogs;

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

}
