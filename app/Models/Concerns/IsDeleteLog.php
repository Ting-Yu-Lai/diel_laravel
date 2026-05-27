<?php

namespace App\Models\Concerns;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait IsDeleteLog
{
    public $timestamps = false;

    public function initializeIsDeleteLog(): void
    {
        $this->casts['created_at'] = 'datetime';
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'deleted_by_admin_id');
    }
}
