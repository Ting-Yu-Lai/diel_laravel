<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasDeleteLogs
{
    public function deleteLogs(): HasMany
    {
        return $this->hasMany('App\\Models\\' . class_basename(static::class) . 'DeleteLog');
    }
}
