<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'loan_name',
        'loan_amount',
        'installment_number',
        'installment_type',
        'installment_amount',
        'due_amount',
    ];

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}
