<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriorityProductBudget extends Model
{
    protected $fillable = [
        'year',
        'month',
        'extra_budget',
    ];
}
