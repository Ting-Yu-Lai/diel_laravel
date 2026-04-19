<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffDeleteLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'staff_name',
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

    public function staff()
    {
        return $this->belongsTo(Staff::class)->withTrashed();
    }
}
