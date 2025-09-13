<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyOfflineCost extends Model
{
    protected $fillable = [
        'year',
        'month',
        'last_date',
        'category_id',
        'amount',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(OfflineCostCategory::class, 'category_id');
    }
}
