<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProject extends Model
{
    protected $fillable = [
        'project_name',
        'start_date',
        'initial_amount',
    ];

    
}
