<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = $user->notifications();

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get unread count
        $unreadCount = $user->unreadNotifications()->count();

        // Get notification types for filtering
        $notificationTypes = [
            'document' => 'Document Updates',
            'invoice' => 'Invoice Notifications',
            'message' => 'Message Notifications',
            'review' => 'Review Requests',
            'welcome' => 'Welcome Messages',
            'general' => 'General Notifications'
        ];

        return Inertia::render('Client/Notifications', [
            'notifications' => $notifications,
            'filters' => $request->only(['status', 'type']),
            'unreadCount' => $unreadCount,
            'notificationTypes' => $notificationTypes
        ]);
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403, 'Unauthorized access to notification.');
        }

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(DatabaseNotification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403, 'Unauthorized access to notification.');
        }

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    public function destroyAll()
    {
        auth()->user()->notifications()->delete();

        return back()->with('success', 'All notifications deleted.');
    }
}