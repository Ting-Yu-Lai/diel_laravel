<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory;

    const ROLE_STAFF       = 0;
    const ROLE_MANAGER     = 1;
    const ROLE_SUPER_ADMIN = 2;

    protected $table = 'admins';
    public $timestamps = true;

    protected $fillable = [
        'username',
        'password_hash',
        'full_name',
        'power',
        'email',
        'last_login_at',
    ];

    protected $hidden = ['password_hash'];

    public function roleName(): string
    {
        return match ((int) $this->power) {
            self::ROLE_SUPER_ADMIN => '超級管理員',
            self::ROLE_MANAGER     => '店長',
            default                => '員工',
        };
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'power'     => $this->power,
            'role_name' => $this->roleName(),
            'full_name' => $this->full_name,
        ];
    }
}
