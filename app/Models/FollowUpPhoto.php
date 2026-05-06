<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUpPhoto extends Model
{
    protected $fillable = [
        'follow_up_id',
        'follow_up_log_id',
        'photo_url',
        'category',
    ];

    public function followUp()
    {
        return $this->belongsTo(FollowUp::class);
    }

    public function followUpLog()
    {
        return $this->belongsTo(FollowUpLog::class);
    }
}
