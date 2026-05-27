<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait FormatsPhone
{
    public function formattedPhone(): Attribute
    {
        return Attribute::get(fn() => static::fmtPhone($this->phone));
    }

    protected static function fmtPhone(?string $p): ?string
    {
        if ($p && strlen($p) === 10) {
            return substr($p, 0, 4) . '-' . substr($p, 4, 3) . '-' . substr($p, 7, 3);
        }
        return $p;
    }
}
