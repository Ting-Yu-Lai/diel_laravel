<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUpLog extends Model
{
    protected $fillable = [
        'follow_up_id',
        'day_number',
        'content',
    ];

    public function followUp()
    {
        return $this->belongsTo(FollowUp::class);
    }

    public function photos()
    {
        return $this->hasMany(FollowUpPhoto::class);
    }
}
