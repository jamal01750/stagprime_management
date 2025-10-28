<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'image',
        'student_id',
        'student_name',
        'phone',
        'alt_Phone',
        'nid_birth',
        'address',
        'batch_id',
        'course_id',
        'batch_time',
        'admission_date',
        'total_fee',
        'paid_amount',
        'due_amount',
        'payment_due_date',
        'description',
        'payment_status',
        'active_status',
        'approve_status',
    ];

    // Relationships
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function payments()
    {
        return $this->hasMany(StudentPayment::class, 'student_id');
    }

}
