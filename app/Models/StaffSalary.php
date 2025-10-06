<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'salary_date',
        'amount',
        'status',
        'approve_status',
        'paid_date',
        'payment_method',
        'note'
    ];

    public function staff() {
        return $this->belongsTo(Staff::class);
    }
}
