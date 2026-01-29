<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Client;
use App\Services\AdminNotificationService;
use App\Mail\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MessageController extends Controller
{
    protected $adminNotificationService;

    public function __construct(AdminNotificationService $adminNotificationService)
    {
        $this->adminNotificationService = $adminNotificationService;
    }
    public function index(Request $request)
    {
        $client = Client::where('user_id', auth()->id())->first();
        
        if (!$client) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Client profile not found.');
        }

        $query = Message::where('client_id', $client->id)
            ->where(function ($q) {
                $q->where('sender_id', auth()->id())
                  ->orWhere('recipient_id', auth()->id());
            })
            ->with(['sender', 'recipient']);

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('recipient_id', auth()->id())->unread();
            } elseif ($request->status === 'read') {
                $query->where('recipient_id', auth()->id())->where('is_read', true);
            }
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        $messages = $query->latest()->paginate(10);

        // Get tax professionals for new message
        $taxProfessionals = User::where('role', 'admin')
            ->orWhere('role', 'tax_professional')
            ->select('id', 'first_name', 'last_name', 'middle_name', 'email')
            ->get();

        // Get unread count
        $unreadCount = Message::where('client_id', $client->id)
            ->where('recipient_id', auth()->id())
            ->unread()
            ->count();

        return Inertia::render('Client/Messages', [
            'messages' => $messages,
            'filters' => $request->only(['status', 'priority']),
            'taxProfessionals' => $taxProfessionals,
            'unreadCount' => $unreadCount
        ]);
    }

    public function store(Request $request)
    {
        $client = Client::where('user_id', auth()->id())->first();
        
        if (!$client) {
            return back()->with('error', 'Client profile not found.');
        }

        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:2000',
            'priority' => 'required|in:low,normal,high'
        ]);

        $message = Message::create([
            'client_id' => $client->id,
            'sender_id' => auth()->id(),
            'recipient_id' => $request->recipient_id,
            'subject' => $request->subject,
            'body' => $request->body,
            'priority' => $request->priority
        ]);

        // Notify admins about new message from client
        $this->adminNotificationService->notifyMessageSent($message);

        // Send email notification to the specific admin recipient
        $recipient = User::find($request->recipient_id);
        if ($recipient) {
            try {
                Mail::to($recipient->email)->send(new NewMessageNotification($message, $recipient));
            } catch (\Exception $e) {
                \Log::error('Failed to send message notification email to admin', [
                    'message_id' => $message->id,
                    'admin_email' => $recipient->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return back()->with('success', 'Message sent successfully.');
    }

    public function show(Message $message)
    {
        $client = Client::where('user_id', auth()->id())->first();
        
        if (!$client) {
            abort(403, 'Client profile not found.');
        }

        // Check if user is sender or recipient AND the message belongs to this client
        if (($message->sender_id != auth()->id() && $message->recipient_id != auth()->id()) 
            || $message->client_id != $client->id) {
            abort(403, 'Unauthorized access to message.');
        }

        // Mark as read if user is recipient
        if ($message->recipient_id == auth()->id() && !$message->is_read) {
            $message->markAsRead();
        }

        $message->load(['sender', 'recipient']);

        // Get the conversation history between the same participants for this client
        $conversation = Message::where('client_id', $client->id)
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

        return Inertia::render('Client/MessageDetail', [
            'message' => $message,
            'conversation' => $conversation
        ]);
    }

    public function reply(Request $request, Message $originalMessage)
    {
        $request->validate([
            'body' => 'required|string|max:2000'
        ]);

        // For client replies, always send to admin users
        $currentUserId = auth()->id();
        
        // Get the first admin user as recipient
        $adminUser = \App\Models\User::where('role', 'admin')->first();
        $replyRecipientId = $adminUser->id;
        
        // Get client_id from current user
        $client = \App\Models\Client::where('user_id', $currentUserId)->first();
        $clientId = $client->id;

        $replyMessage = Message::create([
            'client_id' => $clientId,
            'sender_id' => $currentUserId,
            'recipient_id' => $replyRecipientId,
            'subject' => 'Re: ' . ($originalMessage->subject ?: 'Message'),
            'body' => $request->body,
            'priority' => $originalMessage->priority ?: 'normal'
        ]);

        // Send email notification to admin
        $adminUser = User::find($replyRecipientId);
        if ($adminUser) {
            try {
                Mail::to($adminUser->email)->send(new NewMessageNotification($replyMessage, $adminUser));
            } catch (\Exception $e) {
                \Log::error('Failed to send reply notification email to admin', [
                    'message_id' => $replyMessage->id,
                    'admin_email' => $adminUser->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return back()->with('success', 'Reply sent successfully.');
    }

    public function markAsRead(Message $message)
    {
        $client = Client::where('user_id', auth()->id())->first();
        
        if (!$client || $message->client_id != $client->id || $message->recipient_id != auth()->id()) {
            abort(403, 'Unauthorized access to message.');
        }

        $message->markAsRead();

        return back()->with('success', 'Message marked as read.');
    }
}