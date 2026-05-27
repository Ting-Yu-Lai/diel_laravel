<?php

namespace App\Models;

use App\Models\Concerns\HasDeleteLogs;
use Illuminate\Database\Eloquent\Model;

class TreatmentCategory extends Model
{
    use HasDeleteLogs;
    protected $fillable = ['name'];

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function deleteLogs()
    {
        return $this->hasMany(TreatmentCategoryDeleteLog::class, 'category_id');
    }

}
