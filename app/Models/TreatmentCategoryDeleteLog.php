<?php

namespace App\Models;

use App\Models\Concerns\IsDeleteLog;
use Illuminate\Database\Eloquent\Model;

class TreatmentCategoryDeleteLog extends Model
{
    use IsDeleteLog;

    protected $fillable = [
        'category_id',
        'category_name',
        'deleted_by_admin_id',
        'reason',
    ];
}
