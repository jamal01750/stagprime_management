<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternshipRegistration extends Model
{
    protected $fillable = [
        'image',
        'intern_id',
        'internee_name',
        'phone',
        'alt_Phone',
        'nid_birth',
        'address',
        'batch_id',
        'course_id',
        'batch_time',
        'admission_date',
        'pay_amount',
        'total_paid',
        'paid_date',
        'paid_date2',
        'paid_date3',
        'description',
        'payment_status',
        'active_status',
    ];
}
