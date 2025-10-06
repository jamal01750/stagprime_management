<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProjectTransaction extends Model
{
    protected $fillable = [
        'project_id',
        'date',
        'type',
        'amount',
        'description',
        'approve_status',
    ];

    public function project()
    {
        return $this->belongsTo(CompanyProject::class, 'project_id');
    }
}
