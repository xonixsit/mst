<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with(['client', 'user', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
        ];

        return Inertia::render('Admin/SupportTickets/Index', [
            'tickets' => $tickets,
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        $clients = \App\Models\Client::with('user:id,first_name,last_name,email')
            ->get(['id', 'user_id'])
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name,
                    'email' => $client->email,
                ];
            });

        return Inertia::render('Admin/SupportTickets/Create', [
            'clients' => $clients,
        ]);
    }

    public function show(SupportTicket $supportTicket)
    {
        $supportTicket->load(['client', 'user', 'assignedTo', 'replies.user']);

        $taxProfessionals = User::where('role', 'tax_professional')
            ->orWhere('role', 'admin')
            ->get(['id', 'first_name', 'last_name', 'email']);

        return Inertia::render('Admin/SupportTickets/Show', [
            'ticket' => $supportTicket,
            'taxProfessionals' => $taxProfessionals,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:billing,technical,general,documentation',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket = SupportTicket::create([
            ...$validated,
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => auth()->id(),
            'status' => 'open',
        ]);

        return redirect()->route('admin.support-tickets.show', $ticket)
            ->with('success', 'Support ticket created successfully.');
    }

    public function updateStatus(SupportTicket $supportTicket, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        if ($validated['status'] === 'resolved') {
            $supportTicket->resolve(auth()->user());
        } elseif ($validated['status'] === 'closed') {
            $supportTicket->close();
        } elseif ($validated['status'] === 'open') {
            $supportTicket->reopen();
        } else {
            $supportTicket->update(['status' => $validated['status']]);
        }

        return back()->with('success', 'Ticket status updated successfully.');
    }

    public function assign(SupportTicket $supportTicket, Request $request)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['assigned_to']);
        $supportTicket->assign($user);

        return back()->with('success', 'Ticket assigned successfully.');
    }

    public function updatePriority(SupportTicket $supportTicket, Request $request)
    {
        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $supportTicket->updatePriority($validated['priority']);

        return back()->with('success', 'Priority updated successfully.');
    }

    public function reply(SupportTicket $supportTicket, Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $supportTicket->addReply(auth()->user(), $validated['message']);

        return back()->with('success', 'Reply added successfully.');
    }
}
