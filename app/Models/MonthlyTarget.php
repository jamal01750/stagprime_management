<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'target_amount',
    ];
}




// bash: php artisan make:command GenerateMonthlyTarget
