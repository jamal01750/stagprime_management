<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfflineCostCategory extends Model
{
    protected $fillable = [
        'category',
    ];

    public function monthlyOfflineCosts()
    {
        return $this->hasMany(MonthlyOfflineCost::class, 'category_id');
    }
}
