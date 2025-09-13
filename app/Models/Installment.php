<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $fillable = [
        'loan_id',
        'installment_amount',
        'due_amount',
        'pay_date',
        'next_date',
        'status'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}

