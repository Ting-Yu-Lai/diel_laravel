<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    //支援 Laravel factory，方便測試或假資料生成
    use HasFactory;

    //
    protected $table = 'admins';
    protected $primaryKey = 'admin_id';
    public $timestamps = true;

    // 批量賦值白名單 只有白名單才可以修改
    protected $fillable = [
        'username',
        'password_hash',
        'full_name',
        'power',
        'email',
        'last_login_at'
    ];
}
