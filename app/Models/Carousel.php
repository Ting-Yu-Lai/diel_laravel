<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'title',
        'image_url',
        'link',
        'order_num',
        'is_active',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];
}
