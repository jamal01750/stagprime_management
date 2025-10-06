<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyOnlineCost extends Model
{
    protected $fillable = [
        'year',
        'month',
        'category_id',
        'activate_date',
        'expire_date',
        'activate_type',
        'activate_cost',
        'amount_type',
        'amount',
        'description',
        'paid_date',
        'status',
        'approve_status',
    ];

    public function category()
    {
        return $this->belongsTo(OnlineCostCategory::class, 'category_id');
    }

    
}
