<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberPointsLog extends Model
{
    protected $fillable = [
        'member_id',
        'type',
        'points',
        'balance_after',
        'source',
        'source_id',
        'note',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
