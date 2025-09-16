<?php

namespace App\Http\Controllers;

use App\Models\OfflinePaymentNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $red = OfflinePaymentNotification::with('monthlyOfflineCost.category')
            ->where('level','red')
            ->where('status','active')
            ->latest()
            ->paginate(5, ['*'], 'red_page');

        $yellow = OfflinePaymentNotification::with('monthlyOfflineCost.category')
            ->where('level','yellow')
            ->where('status','active')
            ->latest()
            ->paginate(5, ['*'], 'yellow_page');

        $green = OfflinePaymentNotification::with('monthlyOfflineCost.category')
            ->where('level','green')
            ->where('status','active')
            ->latest()
            ->paginate(5, ['*'], 'green_page');

        return view('notifications.index', compact('red','yellow','green'));
    }
}
