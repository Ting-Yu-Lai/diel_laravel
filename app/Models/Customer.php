<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'gender',
        'birth_date',
        'phone',
        'email',
        'id_number',
        'address',
        'occupation',
        'emergency_contact',
        'emergency_phone',
        'blood_type',
        'allergies',
        'medical_history',
        'source',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active'  => 'boolean',
    ];
}
