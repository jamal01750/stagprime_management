<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffCategory extends Model
{
    protected $fillable = ['category'];

    /**
     * Get the staff members associated with this category.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}
