<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AdminClientRegisteredNotification;
use App\Notifications\AdminDocumentUploadedNotification;
use App\Notifications\AdminInvoicePaidNotification;
use App\Notifications\AdminMessageSentNotification;
use App\Notifications\AdminReviewRequestNotification;
use Illuminate\Support\Facades\Notification;

class AdminNotificationService
{
    /**
     * Get all admin users who should receive notifications
     */
    public function getAdminUsers()
    {
        return User::whereIn('role', ['admin', 'tax_professional'])->get();
    }

    /**
     * Notify admins about new client registration
     */
    public function notifyClientRegistered(User $client): void
    {
        $admins = $this->getAdminUsers();
        
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new AdminClientRegisteredNotification($client));
        }
    }

    /**
     * Notify admins about new document upload
     */
    public function notifyDocumentUploaded($document): void
    {
        $admins = $this->getAdminUsers();
        
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new AdminDocumentUploadedNotification($document));
        }
    }

    /**
     * Notify admins about invoice payment
     */
    public function notifyInvoicePaid($invoice): void
    {
        $admins = $this->getAdminUsers();
        
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new AdminInvoicePaidNotification($invoice));
        }
    }

    /**
     * Notify admins about client message
     */
    public function notifyMessageSent($message): void
    {
        $admins = $this->getAdminUsers();
        
        if ($admins->isNotEmpty()) {
            $message->load('sender', 'recipient');
            Notification::send($admins, new AdminMessageSentNotification($message));
        }
    }

    /**
     * Send notification to specific admin
     */
    public function notifySpecificAdmin(User $admin, $notification): void
    {
        if ($admin->isTaxProfessional()) {
            $admin->notify($notification);
        }
    }

    /**
     * Notify admins about review request
     */
    public function notifyReviewRequested($client, array $reviewData = []): void
    {
        $admins = $this->getAdminUsers();
        
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new AdminReviewRequestNotification($client, $reviewData));
        }
    }

    /**
     * Send notification to all admins except the sender
     */
    public function notifyOtherAdmins(User $excludeUser, $notification): void
    {
        $admins = $this->getAdminUsers()->where('id', '!=', $excludeUser->id);
        
        if ($admins->isNotEmpty()) {
            Notification::send($admins, $notification);
        }
    }
}