<?php

namespace App\Models;

use App\Models\Concerns\IsDeleteLog;
use Illuminate\Database\Eloquent\Model;

class StaffDeleteLog extends Model
{
    use IsDeleteLog;

    protected $fillable = [
        'staff_id',
        'staff_name',
        'deleted_by_admin_id',
        'reason',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class)->withTrashed();
    }
}
