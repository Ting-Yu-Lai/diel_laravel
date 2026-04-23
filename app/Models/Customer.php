<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'member_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active'  => 'boolean',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'customer_tag');
    }

    public function treatmentRecords()
    {
        return $this->hasMany(TreatmentRecord::class);
    }
}
