<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\CommunicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CommunicationController extends Controller
{
    public function __construct(
        private CommunicationService $communicationService
    ) {}

    /**
     * Update client communication preferences
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user has client role
        if (!$user->hasRole('client')) {
            abort(403, 'Access denied. Client role required.');
        }
        
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return response()->json(['error' => 'Client record not found.'], 404);
        }

        // Check authorization
        Gate::authorize('update', $client);

        // Validate the request data
        $validatedData = $request->validate([
            'email_notifications_enabled' => 'boolean',
            'sms_notifications_enabled' => 'boolean',
            'preferred_communication_method' => 'in:email,sms,phone',
            'communication_preferences' => 'array',
            'communication_preferences.email_notifications' => 'boolean',
            'communication_preferences.document_notifications' => 'boolean',
            'communication_preferences.invoice_notifications' => 'boolean',
            'communication_preferences.reminder_notifications' => 'boolean',
            'communication_preferences.notification_frequency' => 'in:immediate,daily,weekly',
        ]);

        try {
            // Update client communication preferences
            $client->update([
                'email_notifications_enabled' => $validatedData['email_notifications_enabled'] ?? $client->email_notifications_enabled,
                'sms_notifications_enabled' => $validatedData['sms_notifications_enabled'] ?? $client->sms_notifications_enabled,
                'preferred_communication_method' => $validatedData['preferred_communication_method'] ?? $client->preferred_communication_method,
                'last_communication_at' => now(),
            ]);

            // Update communication preferences if provided
            if (isset($validatedData['communication_preferences'])) {
                $client->updateCommunicationPreferences($validatedData['communication_preferences']);
            }

            Log::info('Client communication preferences updated', [
                'client_id' => $client->id,
                'user_id' => $user->id,
                'preferences' => $validatedData
            ]);

            return response()->json([
                'message' => 'Communication preferences updated successfully',
                'preferences' => $client->getCommunicationPreferences()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update communication preferences', [
                'client_id' => $client->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to update preferences'], 500);
        }
    }

    /**
     * Get client communication preferences
     */
    public function getPreferences()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('client')) {
            abort(403, 'Access denied. Client role required.');
        }
        
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return response()->json(['error' => 'Client record not found.'], 404);
        }

        Gate::authorize('view', $client);

        return response()->json([
            'preferences' => $client->getCommunicationPreferences(),
            'email_notifications_enabled' => $client->email_notifications_enabled,
            'sms_notifications_enabled' => $client->sms_notifications_enabled,
            'preferred_communication_method' => $client->preferred_communication_method,
            'last_communication_at' => $client->last_communication_at,
        ]);
    }

    /**
     * Get communication history for client
     */
    public function getHistory()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('client')) {
            abort(403, 'Access denied. Client role required.');
        }
        
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return response()->json(['error' => 'Client record not found.'], 404);
        }

        Gate::authorize('view', $client);

        try {
            $history = $this->communicationService->getCommunicationHistory($client);

            return response()->json([
                'history' => $history,
                'client_id' => $client->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get communication history', [
                'client_id' => $client->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to load communication history'], 500);
        }
    }
}