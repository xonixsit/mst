<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CommunicationService
{
    /**
     * Send automated notification to client based on client information updates
     */
    public function sendClientInformationUpdateNotification(Client $client, array $changes): bool
    {
        try {
            $preferences = $this->getClientCommunicationPreferences($client);
            
            if (!$preferences['email_notifications']) {
                return false;
            }

            $subject = 'Your Information Has Been Updated';
            $body = $this->generateUpdateNotificationBody($client, $changes);
            
            // Send internal message
            $this->sendInternalMessage($client, $subject, $body, 'normal');
            
            // Send email if enabled
            if ($preferences['email_enabled'] && $client->email) {
                $this->sendEmail($client->email, $subject, $body);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send client information update notification', [
                'client_id' => $client->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send document status notification
     */
    public function sendDocumentStatusNotification(Client $client, string $documentName, string $status): bool
    {
        try {
            $preferences = $this->getClientCommunicationPreferences($client);
            
            if (!$preferences['document_notifications']) {
                return false;
            }

            $subject = "Document Status Update: {$documentName}";
            $body = $this->generateDocumentStatusNotificationBody($client, $documentName, $status);
            
            // Send internal message
            $this->sendInternalMessage($client, $subject, $body, 'normal');
            
            // Send email if enabled
            if ($preferences['email_enabled'] && $client->email) {
                $this->sendEmail($client->email, $subject, $body);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send document status notification', [
                'client_id' => $client->id,
                'document' => $documentName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send invoice notification
     */
    public function sendInvoiceNotification(Client $client, $invoice): bool
    {
        try {
            $preferences = $this->getClientCommunicationPreferences($client);
            
            if (!$preferences['invoice_notifications']) {
                return false;
            }

            $subject = "New Invoice: {$invoice->invoice_number}";
            $body = $this->generateInvoiceNotificationBody($client, $invoice);
            
            // Send internal message
            $this->sendInternalMessage($client, $subject, $body, 'high');
            
            // Send email if enabled
            if ($preferences['email_enabled'] && $client->email) {
                $this->sendEmail($client->email, $subject, $body);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send invoice notification', [
                'client_id' => $client->id,
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send reminder for missing documents
     */
    public function sendMissingDocumentReminder(Client $client, array $missingDocuments): bool
    {
        try {
            $preferences = $this->getClientCommunicationPreferences($client);
            
            if (!$preferences['reminder_notifications']) {
                return false;
            }

            $subject = 'Missing Documents Reminder';
            $body = $this->generateMissingDocumentReminderBody($client, $missingDocuments);
            
            // Send internal message
            $this->sendInternalMessage($client, $subject, $body, 'normal');
            
            // Send email if enabled
            if ($preferences['email_enabled'] && $client->email) {
                $this->sendEmail($client->email, $subject, $body);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send missing document reminder', [
                'client_id' => $client->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get client communication preferences
     */
    public function getClientCommunicationPreferences(Client $client): array
    {
        $preferences = $client->getCommunicationPreferences();
        
        return [
            'email_enabled' => $client->email_notifications_enabled ?? true,
            'email_notifications' => $preferences['email_notifications'] ?? true,
            'document_notifications' => $preferences['document_notifications'] ?? true,
            'invoice_notifications' => $preferences['invoice_notifications'] ?? true,
            'reminder_notifications' => $preferences['reminder_notifications'] ?? true,
            'sms_enabled' => $client->sms_notifications_enabled ?? false,
            'preferred_method' => $client->preferred_communication_method ?? 'email',
            'notification_frequency' => $preferences['notification_frequency'] ?? 'immediate',
        ];
    }

    /**
     * Update client communication preferences
     */
    public function updateClientCommunicationPreferences(Client $client, array $preferences): bool
    {
        try {
            // In a real implementation, save preferences to database
            // For now, we'll just log the update
            Log::info('Client communication preferences updated', [
                'client_id' => $client->id,
                'preferences' => $preferences
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update communication preferences', [
                'client_id' => $client->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get communication history for client
     */
    public function getCommunicationHistory(Client $client): array
    {
        $messages = Message::where('client_id', $client->id)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'total_messages' => $messages->count(),
            'unread_messages' => $messages->where('is_read', false)->count(),
            'messages_by_priority' => $messages->groupBy('priority')->map->count(),
            'recent_messages' => $messages->take(10)->values(),
            'communication_timeline' => $this->buildCommunicationTimeline($messages),
        ];
    }

    /**
     * Send internal message through the platform
     */
    private function sendInternalMessage(Client $client, string $subject, string $body, string $priority): void
    {
        // Get the assigned tax professional or default admin
        $recipient = $client->user ?? User::where('role', 'admin')->first();
        
        if (!$recipient) {
            return;
        }

        Message::create([
            'client_id' => $client->id,
            'sender_id' => $recipient->id, // System message from admin
            'recipient_id' => $client->user_id ?? $client->id, // To client
            'subject' => $subject,
            'body' => $body,
            'priority' => $priority,
        ]);
    }

    /**
     * Send email notification
     */
    private function sendEmail(string $email, string $subject, string $body): void
    {
        // In a real implementation, use Laravel Mail
        // For now, just log the email
        Log::info('Email notification sent', [
            'to' => $email,
            'subject' => $subject,
            'body' => $body
        ]);
    }

    /**
     * Generate notification body for information updates
     */
    private function generateUpdateNotificationBody(Client $client, array $changes): string
    {
        $clientName = $client->full_name;
        $body = "Dear {$clientName},\n\n";
        $body .= "Your client information has been updated. The following changes were made:\n\n";
        
        foreach ($changes as $field => $change) {
            $body .= "• {$field}: {$change['old']} → {$change['new']}\n";
        }
        
        $body .= "\nIf you have any questions about these changes, please contact your tax professional.\n\n";
        $body .= "Best regards,\nYour Tax Consulting Team";
        
        return $body;
    }

    /**
     * Generate notification body for document status updates
     */
    private function generateDocumentStatusNotificationBody(Client $client, string $documentName, string $status): string
    {
        $clientName = $client->full_name;
        $body = "Dear {$clientName},\n\n";
        $body .= "The status of your document '{$documentName}' has been updated to: " . ucfirst($status) . "\n\n";
        
        if ($status === 'approved') {
            $body .= "Your document has been reviewed and approved. Thank you for providing the necessary information.\n\n";
        } elseif ($status === 'rejected') {
            $body .= "Your document requires attention. Please review the feedback and resubmit if necessary.\n\n";
        } else {
            $body .= "Your document is currently under review. We will notify you once the review is complete.\n\n";
        }
        
        $body .= "You can view your documents and their status in your client portal.\n\n";
        $body .= "Best regards,\nYour Tax Consulting Team";
        
        return $body;
    }

    /**
     * Generate notification body for invoice updates
     */
    private function generateInvoiceNotificationBody(Client $client, $invoice): string
    {
        $clientName = $client->full_name;
        $body = "Dear {$clientName},\n\n";
        $body .= "A new invoice has been generated for your account:\n\n";
        $body .= "Invoice Number: {$invoice->invoice_number}\n";
        $body .= "Title: {$invoice->title}\n";
        $body .= "Amount: $" . number_format($invoice->total_amount, 2) . "\n";
        $body .= "Due Date: " . $invoice->created_at->addDays(30)->format('M d, Y') . "\n\n";
        $body .= "You can view and pay your invoice through your client portal.\n\n";
        $body .= "Best regards,\nYour Tax Consulting Team";
        
        return $body;
    }

    /**
     * Generate notification body for missing document reminders
     */
    private function generateMissingDocumentReminderBody(Client $client, array $missingDocuments): string
    {
        $clientName = $client->full_name;
        $body = "Dear {$clientName},\n\n";
        $body .= "We noticed that some required documents are still missing from your profile:\n\n";
        
        foreach ($missingDocuments as $document) {
            $body .= "• " . ucfirst(str_replace('_', ' ', $document)) . "\n";
        }
        
        $body .= "\nPlease upload these documents at your earliest convenience to ensure timely processing of your tax return.\n\n";
        $body .= "You can upload documents through your client portal.\n\n";
        $body .= "Best regards,\nYour Tax Consulting Team";
        
        return $body;
    }

    /**
     * Build communication timeline
     */
    private function buildCommunicationTimeline($messages): array
    {
        return $messages->map(function ($message) {
            $senderName = 'Unknown';
            if ($message->sender) {
                $senderName = trim(implode(' ', array_filter([
                    $message->sender->first_name,
                    $message->sender->middle_name,
                    $message->sender->last_name
                ]))) ?: 'Unknown';
            }

            $recipientName = 'Unknown';
            if ($message->recipient) {
                $recipientName = trim(implode(' ', array_filter([
                    $message->recipient->first_name,
                    $message->recipient->middle_name,
                    $message->recipient->last_name
                ]))) ?: 'Unknown';
            }

            return [
                'id' => $message->id,
                'type' => 'message',
                'subject' => $message->subject,
                'sender' => $senderName,
                'recipient' => $recipientName,
                'priority' => $message->priority,
                'is_read' => $message->is_read,
                'created_at' => $message->created_at,
            ];
        })->toArray();
    }
}