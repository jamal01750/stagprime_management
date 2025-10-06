<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientProjectTransaction extends Model
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
        return $this->belongsTo(ClientProject::class, 'project_id');
    }
    
}
