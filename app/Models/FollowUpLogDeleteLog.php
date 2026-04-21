<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUpLogDeleteLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'follow_up_log_id',
        'follow_up_id',
        'deleted_by_admin_id',
        'reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
