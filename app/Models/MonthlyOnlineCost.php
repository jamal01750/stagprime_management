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
        'amount_type',
        'amount',
        'description',
    ];

    
}
