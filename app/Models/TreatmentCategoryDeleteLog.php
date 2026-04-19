<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentCategoryDeleteLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'category_name',
        'deleted_by_admin_id',
        'reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'deleted_by_admin_id', 'admin_id');
    }
}
