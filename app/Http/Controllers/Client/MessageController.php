<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
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
            ->with(['sender:id,name', 'recipient:id,name']);

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

        $messages = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get tax professionals for new message
        $taxProfessionals = User::where('role', 'admin')
            ->orWhere('role', 'tax_professional')
            ->select('id', 'name', 'email')
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

        Message::create([
            'client_id' => $client->id,
            'sender_id' => auth()->id(),
            'recipient_id' => $request->recipient_id,
            'subject' => $request->subject,
            'body' => $request->body,
            'priority' => $request->priority
        ]);

        return back()->with('success', 'Message sent successfully.');
    }

    public function show(Message $message)
    {
        $client = Client::where('user_id', auth()->id())->first();
        
        if (!$client || $message->client_id !== $client->id) {
            abort(403, 'Unauthorized access to message.');
        }

        // Check if user is sender or recipient
        if ($message->sender_id !== auth()->id() && $message->recipient_id !== auth()->id()) {
            abort(403, 'Unauthorized access to message.');
        }

        // Mark as read if user is recipient
        if ($message->recipient_id === auth()->id() && !$message->is_read) {
            $message->markAsRead();
        }

        $message->load(['sender:id,name', 'recipient:id,name']);

        return Inertia::render('Client/MessageDetail', [
            'message' => $message
        ]);
    }

    public function reply(Request $request, Message $originalMessage)
    {
        $client = Client::where('user_id', auth()->id())->first();
        
        if (!$client || $originalMessage->client_id !== $client->id) {
            abort(403, 'Unauthorized access to message.');
        }

        $request->validate([
            'body' => 'required|string|max:2000'
        ]);

        Message::create([
            'client_id' => $client->id,
            'sender_id' => auth()->id(),
            'recipient_id' => $originalMessage->sender_id === auth()->id() 
                ? $originalMessage->recipient_id 
                : $originalMessage->sender_id,
            'subject' => 'Re: ' . $originalMessage->subject,
            'body' => $request->body,
            'priority' => $originalMessage->priority
        ]);

        return back()->with('success', 'Reply sent successfully.');
    }

    public function markAsRead(Message $message)
    {
        $client = Client::where('user_id', auth()->id())->first();
        
        if (!$client || $message->client_id !== $client->id || $message->recipient_id !== auth()->id()) {
            abort(403, 'Unauthorized access to message.');
        }

        $message->markAsRead();

        return back()->with('success', 'Message marked as read.');
    }
}