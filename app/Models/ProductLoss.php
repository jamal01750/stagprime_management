<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLoss extends Model
{
    protected $fillable = [
        'product_category_id',
        'quantity',
        'loss_amount',
        'description'
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
