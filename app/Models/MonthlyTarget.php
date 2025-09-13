<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyTarget extends Model
{
    protected $fillable = [
        'year',
        'month',
        'amount',
    ];

    /**
     * Get the target amount for a specific year and month.
     *
     * @param int $year
     * @param int $month
     * @return float|null
     */
    public static function getTargetAmount(int $year, int $month): ?float
    {
        $target = self::where('year', $year)->where('month', $month)->first();
        return $target ? $target->amount : null;
    }
}
