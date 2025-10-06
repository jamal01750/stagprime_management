<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'name',
        'designation',
        'category_id',
        'join_date',
        'amount',
        'description',
        'approve_status',
    ];

    public function category()
    {
        return $this->belongsTo(StaffCategory::class);
    }

    
}
