<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDeleteLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'customer_name',
        'deleted_by_admin_id',
        'reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'deleted_by_admin_id');
    }
}
