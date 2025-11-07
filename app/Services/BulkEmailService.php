<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class BulkEmailService
{
    /**
     * Send bulk emails to clients.
     *
     * @param array $clientIds
     * @param string $subject
     * @param string $message
     * @param array $options
     * @return array
     */
    public function sendBulkEmails(array $clientIds, string $subject, string $message, array $options = []): array
    {
        $clients = Client::whereIn('id', $clientIds)->get();
        $sent = 0;
        $errors = [];

        foreach ($clients as $client) {
            try {
                $personalizedMessage = $this->personalizeMessage($message, $client);
                $personalizedSubject = $this->personalizeMessage($subject, $client);

                // For now, we'll log the email instead of actually sending it
                // In a real implementation, you would use Laravel's Mail facade
                Log::info('Bulk email sent', [
                    'client_id' => $client->id,
                    'client_email' => $client->email,
                    'subject' => $personalizedSubject,
                    'message_preview' => substr($personalizedMessage, 0, 100) . '...'
                ]);

                // Simulate email sending
                // Mail::to($client->email)->send(new BulkClientEmail($personalizedSubject, $personalizedMessage));

                $sent++;
            } catch (Exception $e) {
                $errors[] = [
                    'client_id' => $client->id,
                    'client_email' => $client->email,
                    'error' => $e->getMessage()
                ];
                Log::error('Failed to send bulk email', [
                    'client_id' => $client->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'sent' => $sent,
            'errors' => $errors,
            'total_attempted' => count($clients)
        ];
    }

    /**
     * Personalize message with client data.
     *
     * @param string $message
     * @param Client $client
     * @return string
     */
    private function personalizeMessage(string $message, Client $client): string
    {
        $replacements = [
            '[Client Name]' => $client->full_name,
            '[First Name]' => $client->first_name,
            '[Last Name]' => $client->last_name,
            '[Email]' => $client->email,
            '[Phone]' => $client->phone,
            '[Date]' => now()->format('F j, Y'),
            '[Time]' => now()->format('g:i A'),
            '[Your Name]' => auth()->user()->name ?? 'Tax Professional',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    /**
     * Get available email templates.
     *
     * @return array
     */
    public function getEmailTemplates(): array
    {
        return [
            'document_request' => [
                'name' => 'Document Request',
                'subject' => 'Document Request - Tax Preparation',
                'message' => 'Dear [Client Name],

We need the following documents to proceed with your tax preparation:

[Document List]

Please upload these documents at your earliest convenience through your client portal.

If you have any questions, please don\'t hesitate to contact us.

Best regards,
[Your Name]'
            ],
            'appointment_reminder' => [
                'name' => 'Appointment Reminder',
                'subject' => 'Appointment Reminder - [Date]',
                'message' => 'Dear [Client Name],

This is a reminder about your upcoming appointment on [Date] at [Time].

Please bring any requested documents and arrive 10 minutes early.

If you need to reschedule, please contact us as soon as possible.

Best regards,
[Your Name]'
            ],
            'status_update' => [
                'name' => 'Status Update',
                'subject' => 'Status Update - Your Tax Return',
                'message' => 'Dear [Client Name],

We wanted to update you on the status of your tax return preparation.

Current Status: [Status Details]

Next Steps: [Next Steps]

If you have any questions, please don\'t hesitate to contact us.

Best regards,
[Your Name]'
            ],
            'welcome' => [
                'name' => 'Welcome Message',
                'subject' => 'Welcome to Our Tax Services',
                'message' => 'Dear [Client Name],

Welcome to our tax consulting services! We\'re excited to work with you this tax season.

To get started, please:
1. Log into your client portal
2. Complete your personal information
3. Upload any available tax documents

Our team will review your information and contact you within 2 business days.

Best regards,
[Your Name]'
            ],
            'tax_season_reminder' => [
                'name' => 'Tax Season Reminder',
                'subject' => 'Tax Season is Here - Let\'s Get Started',
                'message' => 'Dear [Client Name],

Tax season is upon us! We\'re ready to help you with your tax preparation.

Important Deadlines:
- Individual Tax Returns: April 15th
- Business Tax Returns: March 15th

Please gather your tax documents and schedule your appointment at your earliest convenience.

Best regards,
[Your Name]'
            ]
        ];
    }

    /**
     * Send document request emails to clients.
     *
     * @param array $clientIds
     * @param array $documentTypes
     * @return array
     */
    public function sendDocumentRequests(array $clientIds, array $documentTypes): array
    {
        $clients = Client::whereIn('id', $clientIds)->get();
        $sent = 0;
        $errors = [];

        $documentLabels = [
            'w2' => 'W-2 Forms',
            '1099' => '1099 Forms',
            'receipts' => 'Receipts and Expense Documentation',
            'bank_statements' => 'Bank Statements',
            'tax_returns' => 'Previous Year Tax Returns',
            'id_documents' => 'Identification Documents'
        ];

        $documentList = collect($documentTypes)
            ->map(fn($type) => '• ' . ($documentLabels[$type] ?? $type))
            ->join("\n");

        $subject = 'Document Request - Tax Preparation';
        $message = "Dear [Client Name],

We need the following documents to proceed with your tax preparation:

{$documentList}

Please upload these documents through your client portal at your earliest convenience.

If you have any questions about these documents or need assistance with uploading, please don't hesitate to contact us.

Best regards,
[Your Name]";

        foreach ($clients as $client) {
            try {
                $personalizedMessage = $this->personalizeMessage($message, $client);
                $personalizedSubject = $this->personalizeMessage($subject, $client);

                // Log the document request
                Log::info('Document request sent', [
                    'client_id' => $client->id,
                    'client_email' => $client->email,
                    'document_types' => $documentTypes,
                    'subject' => $personalizedSubject
                ]);

                // Simulate email sending
                // Mail::to($client->email)->send(new DocumentRequestEmail($personalizedSubject, $personalizedMessage, $documentTypes));

                $sent++;
            } catch (Exception $e) {
                $errors[] = [
                    'client_id' => $client->id,
                    'client_email' => $client->email,
                    'error' => $e->getMessage()
                ];
                Log::error('Failed to send document request', [
                    'client_id' => $client->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'sent' => $sent,
            'errors' => $errors,
            'total_attempted' => count($clients),
            'document_types' => $documentTypes
        ];
    }

    /**
     * Send status update emails to clients.
     *
     * @param array $clientIds
     * @param string $status
     * @param string $customMessage
     * @return array
     */
    public function sendStatusUpdates(array $clientIds, string $status, string $customMessage = ''): array
    {
        $clients = Client::whereIn('id', $clientIds)->get();
        $sent = 0;
        $errors = [];

        $statusMessages = [
            'active' => 'Your account has been activated and we\'re ready to begin working on your tax return.',
            'inactive' => 'Your account has been temporarily deactivated. Please contact us if you have any questions.',
            'archived' => 'Your account has been archived. All your data has been securely stored.',
        ];

        $defaultMessage = $statusMessages[$status] ?? 'Your account status has been updated.';
        $messageToSend = $customMessage ?: $defaultMessage;

        $subject = 'Account Status Update';
        $message = "Dear [Client Name],

We wanted to inform you about an update to your account status.

Status Update: {$messageToSend}

If you have any questions or concerns, please don't hesitate to contact us.

Best regards,
[Your Name]";

        foreach ($clients as $client) {
            try {
                $personalizedMessage = $this->personalizeMessage($message, $client);
                $personalizedSubject = $this->personalizeMessage($subject, $client);

                // Log the status update notification
                Log::info('Status update notification sent', [
                    'client_id' => $client->id,
                    'client_email' => $client->email,
                    'new_status' => $status,
                    'subject' => $personalizedSubject
                ]);

                $sent++;
            } catch (Exception $e) {
                $errors[] = [
                    'client_id' => $client->id,
                    'client_email' => $client->email,
                    'error' => $e->getMessage()
                ];
                Log::error('Failed to send status update notification', [
                    'client_id' => $client->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'sent' => $sent,
            'errors' => $errors,
            'total_attempted' => count($clients),
            'status' => $status
        ];
    }
}