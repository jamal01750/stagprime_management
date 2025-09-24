<?php

namespace App\Http\Controllers;

use App\Models\OfflinePaymentNotification;
use App\Models\PriorityNotification;

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

        // ✅ Add priority notifications
        $priority = PriorityNotification::with('product')
            ->where('is_active', true)
            ->latest()
            ->paginate(5, ['*'], 'priority_page');

        return view('notifications.index', compact('red','yellow','green','priority'));
    }
}
