<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportTicketController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            // Try to find client by user_id in case relationship isn't set
            $client = \App\Models\Client::where('user_id', $user->id)->first();
            
            if (!$client) {
                abort(403, 'No associated client found.');
            }
        }

        $tickets = SupportTicket::where('client_id', $client->id)
            ->with(['assignedTo', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => SupportTicket::where('client_id', $client->id)->count(),
            'open' => SupportTicket::where('client_id', $client->id)->where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('client_id', $client->id)->where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('client_id', $client->id)->where('status', 'resolved')->count(),
        ];

        return Inertia::render('Client/SupportTickets/Index', [
            'tickets' => $tickets,
            'stats' => $stats,
        ]);
    }

    public function show(SupportTicket $supportTicket)
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            $client = \App\Models\Client::where('user_id', $user->id)->first();
        }

        if (!$client || $supportTicket->client_id !== $client->id) {
            abort(403);
        }

        $supportTicket->load(['assignedTo', 'replies.user']);

        return Inertia::render('Client/SupportTickets/Show', [
            'ticket' => $supportTicket,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            $client = \App\Models\Client::where('user_id', $user->id)->first();
        }

        if (!$client) {
            abort(403, 'No associated client found.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:billing,technical,general,documentation',
            'priority' => 'required|in:low,medium,high',
        ]);

        $ticket = SupportTicket::create([
            ...$validated,
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'client_id' => $client->id,
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        return redirect()->route('client.support-tickets.show', $ticket)
            ->with('success', 'Support ticket created successfully.');
    }

    public function reply(SupportTicket $supportTicket, Request $request)
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            $client = \App\Models\Client::where('user_id', $user->id)->first();
        }

        if (!$client || $supportTicket->client_id !== $client->id) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $supportTicket->addReply($user, $validated['message']);

        return back()->with('success', 'Reply added successfully.');
    }
}
