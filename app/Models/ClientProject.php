<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClientProjectTransaction;

class ClientProject extends Model
{
    protected $fillable = [
        'project_name',
        'currency',
        'start_date',
        'end_date',
        'contract_amount',
        'advance_amount',
    ];

    public function transactions()
    {
        return $this->hasMany(ClientProjectTransaction::class, 'project_id');
    }
    

}
