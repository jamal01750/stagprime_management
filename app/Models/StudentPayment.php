<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'pay_amount',
        'due_amount',
        'pay_date',
        'next_date',
    ];

    // Relationship with Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
