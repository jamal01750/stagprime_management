<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriorityProduct extends Model
{
    protected $fillable = [
        'name',
        'quantity',
        'amount',
        'description',
        'approve_status',
        'is_purchased',
    ];

    // Additional methods or relationships can be defined here
}
