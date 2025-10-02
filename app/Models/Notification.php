<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'notifiable_id',
        'notifiable_type',
        'type',
        'level',
        'message',
        'due_date',
        'days_left',
        'action_route',
        'action_params',
        'status',
        'cleared_at',
        'generated_at',
        'updated_level_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date',
        'action_params' => 'array',
        'generated_at' => 'datetime',
        'updated_level_at' => 'datetime',
        'cleared_at' => 'datetime',
    ];

    /**
     * Get the parent notifiable model (e.g., MonthlyOfflineCost, ClientProjectDebit).
     */
    public function notifiable()
    {
        return $this->morphTo();
    }
}
