<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinePhotoPending extends Model
{
    protected $fillable = ['line_user_id', 'category'];
}
