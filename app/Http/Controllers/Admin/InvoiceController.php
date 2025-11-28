<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {}

    public function index(Request $request)
    {
        $query = Invoice::with(['client.user', 'items']);

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by user_id
        if ($request->filled('user_id')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('client.user', function ($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) LIKE ?", ["%{$search}%"]);
                  });
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')
                         ->paginate(25)
                         ->withQueryString();

        $clients = Client::with('user:id,first_name,last_name,email')
                        ->whereHas('user', function ($query) {
                            $query->where('role', 'client');
                        })
                        ->get()
                        ->groupBy('user_id') // Group by user_id to handle duplicates
                        ->map(function ($clientGroup) {
                            // Take the first (or most recent) client for each user
                            $client = $clientGroup->sortByDesc('created_at')->first();
                            return [
                                'id' => $client->id,
                                'first_name' => $client->user->first_name ?? '',
                                'last_name' => $client->user->last_name ?? '',
                                'email' => $client->user->email ?? '',
                                'name' => trim(($client->user->first_name ?? '') . ' ' . ($client->user->last_name ?? ''))
                            ];
                        })
                        ->sortBy('first_name')
                        ->values();

        $stats = $this->invoiceService->getInvoiceStats();

        // Handle user_id parameter by converting it to client_id
        $filters = $request->only(['client_id', 'status', 'date_from', 'date_to', 'search']);
        if ($request->filled('user_id')) {
            // Find the client_id for this user_id
            $client = Client::where('user_id', $request->user_id)->first();
            if ($client) {
                $filters['client_id'] = $client->id;
            }
        }

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
            'clients' => $clients,
            'stats' => $stats,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request)
    {
        $clients = Client::with('user:id,first_name,last_name,email')
                        ->whereHas('user', function ($query) {
                            $query->where('role', 'client');
                        })
                        ->get()
                        ->groupBy('user_id') // Group by user_id to handle duplicates
                        ->map(function ($clientGroup) {
                            // Take the first (or most recent) client for each user
                            $client = $clientGroup->sortByDesc('created_at')->first();
                            return [
                                'id' => $client->id,
                                'first_name' => $client->user->first_name ?? '',
                                'last_name' => $client->user->last_name ?? '',
                                'email' => $client->user->email ?? '',
                                'phone' => $client->phone ?? '',
                                'mobile_number' => $client->mobile_number ?? '',
                                'name' => trim(($client->user->first_name ?? '') . ' ' . ($client->user->last_name ?? ''))
                            ];
                        })
                        ->sortBy('first_name')
                        ->values();

        $defaultServices = $this->invoiceService->getDefaultServices();

        $preselectedClient = null;
        $clientFinancialSummary = null;
        if ($request->filled('client_id')) {
            $preselectedClient = Client::with(['invoices', 'assets', 'expenses'])->find($request->client_id);
            if ($preselectedClient) {
                $clientFinancialSummary = $this->invoiceService->getClientFinancialSummary($preselectedClient);
            }
        }

        return Inertia::render('Admin/Invoices/Create', [
            'clients' => $clients,
            'defaultServices' => $defaultServices,
            'preselectedClient' => $preselectedClient,
            'clientFinancialSummary' => $clientFinancialSummary,
            'currentYear' => date('Y'),
        ]);
    }

    public function store(StoreInvoiceRequest $request)
    {
        $invoice = $this->invoiceService->createInvoice($request->validated());

        if ($request->has('send_email') && $request->send_email) {
            // Redirect to the invoice show page with email modal flag
            return redirect()->route('admin.invoices.show', $invoice->id)
                           ->with('showEmailModal', true);
        }

        return redirect()->route('admin.invoices.index')
                       ->with('success', 'Invoice created successfully!');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client.user', 'items']);

        return Inertia::render('Admin/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['client', 'items']);
        
        $clients = Client::with('user:id,first_name,last_name,email')
                        ->whereHas('user', function ($query) {
                            $query->where('role', 'client');
                        })
                        ->get()
                        ->groupBy('user_id') // Group by user_id to handle duplicates
                        ->map(function ($clientGroup) {
                            // Take the first (or most recent) client for each user
                            $client = $clientGroup->sortByDesc('created_at')->first();
                            return [
                                'id' => $client->id,
                                'first_name' => $client->user->first_name ?? '',
                                'last_name' => $client->user->last_name ?? '',
                                'email' => $client->user->email ?? '',
                                'name' => trim(($client->user->first_name ?? '') . ' ' . ($client->user->last_name ?? ''))
                            ];
                        })
                        ->sortBy('first_name')
                        ->values();

        $defaultServices = $this->invoiceService->getDefaultServices();

        return Inertia::render('Admin/Invoices/Edit', [
            'invoice' => $invoice,
            'clients' => $clients,
            'defaultServices' => $defaultServices,
        ]);
    }

    public function update(StoreInvoiceRequest $request, Invoice $invoice)
    {
        $this->invoiceService->updateInvoice($invoice, $request->validated());

        if ($request->has('send_email') && $request->send_email) {
            $this->invoiceService->sendInvoice($invoice);
            return redirect()->route('admin.invoices.index')
                           ->with('success', 'Invoice updated and sent successfully!');
        }

        return redirect()->route('admin.invoices.index')
                       ->with('success', 'Invoice updated successfully!');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('admin.invoices.index')
                       ->with('success', 'Invoice deleted successfully!');
    }

    public function markAsPaid(Request $request, Invoice $invoice)
    {
        // Validate payment details
        $paymentData = $request->validate([
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'payment_notes' => 'nullable|string',
        ]);

        $invoice->markAsPaid($paymentData);

        return redirect()->back()
                       ->with('success', 'Payment recorded successfully!');
    }

    public function sendEmail(Request $request, Invoice $invoice)
    {
        // Validate the email data from the modal
        $emailData = $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string',
            'payment_link' => 'nullable|url',
            'due_date' => 'nullable|date',
        ]);

        $success = $this->invoiceService->sendInvoice($invoice, $emailData);

        if ($success) {
            return redirect()->back()
                           ->with('success', 'Invoice sent successfully!');
        }

        return redirect()->back()
                       ->with('error', 'Failed to send invoice. Please try again.');
    }

    /**
     * Download invoice as PDF.
     */
    public function download(Invoice $invoice)
    {
        $invoice->load(['client.user', 'items']);
        
        // Generate PDF using DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Show invoices for a specific client (secure route)
     */
    public function clientInvoices(Request $request, int $userId)
    {
        // Redirect to invoices index with client filter
        return redirect()->route('invoices.index', ['client_id' => $userId]);
    }

    /**
     * Show create invoice form for a specific client (secure route)
     */
    public function createForClient(Request $request, int $userId)
    {
        // Redirect to invoice create with client pre-selected
        return redirect()->route('invoices.create', ['client_id' => $userId]);
    }
}