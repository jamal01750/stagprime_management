<?php

namespace App\Http\Controllers;

use App\Models\PriorityNotification;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function index()
    {
        
        $red = Notification::where('status', 'active')
            ->where('level', 'red')
            ->orderBy('updated_level_at', 'desc')
            ->paginate(5, ['*'], 'red_page'); // Paginating the query

        $blue = Notification::where('status', 'active')
            ->where('level', 'blue')
            ->orderBy('updated_level_at', 'desc')
            ->paginate(5, ['*'], 'blue_page'); // Paginating the query

        $green = Notification::where('status', 'active')
            ->where('level', 'green')
            ->orderBy('updated_level_at', 'desc')
            ->paginate(5, ['*'], 'green_page'); // Paginating the query
        
        // ✅ Add priority notifications
        $priority = PriorityNotification::with('product')
            ->where('is_active', true)
            ->latest()
            ->paginate(5, ['*'], 'priority_page');

        return view('notifications.index', compact('red','blue','green','priority'));
    
    }
    

    public function markAsCleared($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update([
            'status' => 'cleared',
            'cleared_at' => now()
        ]);

        return redirect()->back()->with('success', 'Notification marked as cleared.');
    }

    
    

}










    


