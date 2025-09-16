<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfflinePaymentNotification extends Model
{
    protected $fillable = [
        'monthly_offline_cost_id',
        'level',        // green, yellow, red
        'status',       // active / cleared
        'days_left',
        'generated_at',
        'updated_level_at',
        'cleared_at',
    ];

    protected $dates = [
        'generated_at',
        'updated_level_at',
        'cleared_at',
    ];

    public function monthlyOfflineCost()
    {
        return $this->belongsTo(MonthlyOfflineCost::class, 'monthly_offline_cost_id');
    }
}
