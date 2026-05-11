<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'password_hash',
        'full_name',
        'email',
        'phone',
        'address',
        'last_login_at',
        'line_user_id',
        'points_balance',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function formattedPhone(): Attribute
    {
        return Attribute::get(function () {
            $p = $this->phone;
            if ($p && strlen($p) === 10) {
                return substr($p, 0, 4) . '-' . substr($p, 4, 3) . '-' . substr($p, 7, 3);
            }
            return $p;
        });
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function pointsLogs(): HasMany
    {
        return $this->hasMany(MemberPointsLog::class);
    }

    public function redemptionRequests(): HasMany
    {
        return $this->hasMany(MemberRedemptionRequest::class);
    }
}
