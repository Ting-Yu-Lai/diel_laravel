<?php

namespace App\Models;

use App\Models\Concerns\IsDeleteLog;
use Illuminate\Database\Eloquent\Model;

class FollowUpLogDeleteLog extends Model
{
    use IsDeleteLog;

    protected $fillable = [
        'follow_up_log_id',
        'follow_up_id',
        'deleted_by_admin_id',
        'reason',
    ];
}
