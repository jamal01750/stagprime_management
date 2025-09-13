<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProjectDebit extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'currency',
        'pay_amount',
        'due_amount',
        'pay_date',
        'next_date',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(ClientProject::class, 'project_id');
    }
}
