<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Client;
use App\Mail\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = Message::with(['sender', 'recipient', 'client.user']);

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by sender type (client or admin)
        if ($request->filled('sender_type')) {
            if ($request->sender_type === 'client') {
                $query->whereHas('sender', function ($q) {
                    $q->where('role', 'client');
                });
            } elseif ($request->sender_type === 'admin') {
                $query->whereHas('sender', function ($q) {
                    $q->whereIn('role', ['admin', 'tax_professional']);
                });
            }
        }

        // Search by subject or content
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $messages = $query->latest()->paginate(15);

        // Get clients for filter dropdown - only get one client per user
        $clients = Client::with('user')
            ->whereHas('user', function ($query) {
                $query->where('role', 'client'); // Only get actual client users
            })
            ->get()
            ->groupBy('user_id') // Group by user_id to handle duplicates
            ->map(function ($clientGroup) {
                // Take the first (or most recent) client for each user
                $client = $clientGroup->sortByDesc('created_at')->first();
                return [
                    'id' => $client->id,
                    'name' => $client->user->name,
                    'email' => $client->user->email
                ];
            })
            ->values(); // Reset array keys

        // Get statistics
        $stats = [
            'total_messages' => Message::count(),
            'unread_messages' => Message::where('is_read', false)->count(),
            'high_priority' => Message::where('priority', 'high')->count(),
            'client_messages' => Message::whereHas('sender', function ($q) {
                $q->where('role', 'client');
            })->distinct('client_id')->count('client_id')
        ];

        return Inertia::render('Admin/Messages/Index', [
            'messages' => $messages,
            'clients' => $clients,
            'filters' => $request->only(['client_id', 'status', 'priority', 'sender_type', 'search']),
            'stats' => $stats
        ]);
    }

    public function show(Message $message)
    {
        $message->load(['sender', 'recipient', 'client.user']);

        // Mark as read if admin is the recipient
        if ($message->recipient_id == auth()->id() && !$message->is_read) {
            $message->markAsRead();
        }

        // Get conversation history between the same participants for this client
        $conversation = Message::where('client_id', $message->client_id)
            ->where(function ($query) use ($message) {
                // Get messages with the same participants (both directions)
                $query->where(function ($q) use ($message) {
                    $q->where('sender_id', $message->sender_id)
                      ->where('recipient_id', $message->recipient_id);
                })->orWhere(function ($q) use ($message) {
                    $q->where('sender_id', $message->recipient_id)
                      ->where('recipient_id', $message->sender_id);
                });
            })
            ->with(['sender', 'recipient'])
            ->latest()
            ->get();

        return Inertia::render('Admin/Messages/Show', [
            'message' => $message,
            'conversation' => $conversation
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:2000',
            'priority' => 'required|in:low,normal,high'
        ]);

        // Get the client and their user_id
        $client = Client::with('user')->findOrFail($request->client_id);
        
        if (!$client->user) {
            return back()->withErrors(['client_id' => 'Client does not have a user account.']);
        }

        $message = Message::create([
            'client_id' => $request->client_id,
            'sender_id' => auth()->id(),
            'recipient_id' => $client->user->id, // Send to the client's user account
            'subject' => $request->subject,
            'body' => $request->body,
            'priority' => $request->priority
        ]);

        // Send email notification to client if they have email notifications enabled
        if ($client->email_notifications_enabled ?? true) {
            try {
                Mail::to($client->user->email)->send(new NewMessageNotification($message, $client->user));
            } catch (\Exception $e) {
                \Log::error('Failed to send message notification email to client', [
                    'message_id' => $message->id,
                    'client_email' => $client->user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return back()->with('success', 'Message sent successfully.');
    }

    public function reply(Request $request, Message $originalMessage)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
            'client_id' => 'nullable|exists:clients,id'
        ]);

        // Get client_id first
        $clientId = $request->client_id ?: $originalMessage->client_id ?: $originalMessage->client?->id;
        
        // For admin replies, always send to the client's user
        $client = \App\Models\Client::with('user')->find($clientId);
        $replyRecipientId = $client->user->id;
        
        $currentUserId = auth()->id();

        // Client ID already determined above

        $replyMessage = Message::create([
            'client_id' => $clientId,
            'sender_id' => $currentUserId,
            'recipient_id' => $replyRecipientId,
            'subject' => 'Re: ' . ($originalMessage->subject ?: 'Message'),
            'body' => $request->body,
            'priority' => $originalMessage->priority ?: 'normal'
        ]);

        // Send email notification to client if they have email notifications enabled
        if ($client->email_notifications_enabled ?? true) {
            try {
                Mail::to($client->user->email)->send(new NewMessageNotification($replyMessage, $client->user));
            } catch (\Exception $e) {
                \Log::error('Failed to send reply notification email to client', [
                    'message_id' => $replyMessage->id,
                    'client_email' => $client->user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return back()->with('success', 'Reply sent successfully.');
    }

    public function markAsRead(Message $message)
    {
        $message->markAsRead();
        return back()->with('success', 'Message marked as read.');
    }

    public function markMultipleAsRead(Request $request)
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id'
        ]);

        Message::whereIn('id', $request->message_ids)->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return back()->with('success', 'Messages marked as read.');
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return back()->with('success', 'Message deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_read,delete,archive',
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id'
        ]);

        $messages = Message::whereIn('id', $request->message_ids);

        switch ($request->action) {
            case 'mark_read':
                $messages->update(['is_read' => true, 'read_at' => now()]);
                $message = 'Messages marked as read.';
                break;
            case 'delete':
                $messages->delete();
                $message = 'Messages deleted.';
                break;
            case 'archive':
                $messages->update(['archived_at' => now()]);
                $message = 'Messages archived.';
                break;
        }

        return back()->with('success', $message);
    }
}