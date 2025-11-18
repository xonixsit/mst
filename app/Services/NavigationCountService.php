<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class NavigationCountService
{
    public static function getClientCounts(): array
    {
        $user = Auth::user();
        
        if (!$user || !$user->isClient()) {
            return [
                'unread_messages' => 0,
                'unread_notifications' => 0
            ];
        }

        $client = Client::where('user_id', $user->id)->first();
        
        if (!$client) {
            return [
                'unread_messages' => 0,
                'unread_notifications' => 0
            ];
        }

        // Get unread messages count
        $unreadMessages = Message::where('client_id', $client->id)
            ->where('recipient_id', $user->id)
            ->unread()
            ->count();

        // Get unread notifications count
        $unreadNotifications = $user->unreadNotifications()->count();

        return [
            'unread_messages' => $unreadMessages,
            'unread_notifications' => $unreadNotifications
        ];
    }

    public static function getAdminCounts(): array
    {
        $user = Auth::user();
        
        if (!$user || !$user->isTaxProfessional()) {
            return [
                'unread_messages' => 0,
                'unread_notifications' => 0
            ];
        }

        // Get unread messages where admin is the recipient
        $unreadMessages = Message::where('recipient_id', $user->id)
            ->unread()
            ->count();

        // Get unread notifications
        $unreadNotifications = $user->unreadNotifications()->count();

        return [
            'unread_messages' => $unreadMessages,
            'unread_notifications' => $unreadNotifications
        ];
    }
}