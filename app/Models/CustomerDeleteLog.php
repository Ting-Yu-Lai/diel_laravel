<?php

namespace App\Models;

use App\Models\Concerns\IsDeleteLog;
use Illuminate\Database\Eloquent\Model;

class CustomerDeleteLog extends Model
{
    use IsDeleteLog;

    protected $fillable = [
        'customer_id',
        'customer_name',
        'deleted_by_admin_id',
        'reason',
    ];
}
