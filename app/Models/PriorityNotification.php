<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriorityNotification extends Model
{
    protected $fillable = ['priority_product_id', 'is_active'];

    public function product()
    {
        return $this->belongsTo(PriorityProduct::class, 'priority_product_id');
    }
}
