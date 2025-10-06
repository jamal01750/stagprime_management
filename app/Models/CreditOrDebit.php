<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditOrDebit extends Model
{
    //
    use HasFactory;
    
    protected $fillable = [
        'date',
        'type',
        'amount',
        'description',
        'approve_status',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
