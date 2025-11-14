<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices for the authenticated client.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get client record for the authenticated user
        $client = Client::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();
        
        if (!$client) {
            return Inertia::render('Client/Invoices/Index', [
                'invoices' => collect([]),
                'stats' => [
                    'total_invoices' => 0,
                    'total_paid' => 0,
                    'total_pending' => 0,
                    'total_amount' => 0,
                ],
                'filters' => [],
            ]);
        }
        
        $query = Invoice::with(['items'])
            ->where('client_id', $client->id);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by year
        if ($request->filled('year')) {
            $query->where('invoice_year', $request->year);
        }
        
        // Search by invoice number or title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }
        
        $invoices = $query->orderBy('created_at', 'desc')
                         ->paginate(15)
                         ->withQueryString();
        
        // Calculate stats
        $allInvoices = Invoice::where('client_id', $client->id)->get();
        $stats = [
            'total_invoices' => $allInvoices->count(),
            'total_paid' => $allInvoices->where('status', 'paid')->count(),
            'total_pending' => $allInvoices->whereIn('status', ['draft', 'sent'])->count(),
            'total_amount' => $allInvoices->sum('total_amount'),
            'total_paid_amount' => $allInvoices->where('status', 'paid')->sum('total_amount'),
            'total_pending_amount' => $allInvoices->whereIn('status', ['draft', 'sent'])->sum('total_amount'),
        ];
        
        // Get available years for filter
        $availableYears = Invoice::where('client_id', $client->id)
            ->selectRaw('DISTINCT invoice_year')
            ->whereNotNull('invoice_year')
            ->orderBy('invoice_year', 'desc')
            ->pluck('invoice_year');
        
        return Inertia::render('Client/Invoices/Index', [
            'invoices' => $invoices,
            'stats' => $stats,
            'availableYears' => $availableYears,
            'filters' => $request->only(['status', 'year', 'search']),
        ]);
    }
    
    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $user = Auth::user();
        
        // Get client record for the authenticated user
        $client = Client::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();
        
        // Verify that this invoice belongs to the authenticated client
        if (!$client || $invoice->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this invoice.');
        }
        
        $invoice->load(['client', 'items']);
        
        return Inertia::render('Client/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }
    
    /**
     * Download invoice as PDF.
     */
    public function download(Invoice $invoice)
    {
        $user = Auth::user();
        
        // Get client record for the authenticated user
        $client = Client::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();
        
        // Verify that this invoice belongs to the authenticated client
        if (!$client || $invoice->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this invoice.');
        }
        
        $invoice->load(['client', 'items']);
        
        // Generate PDF content (simplified version)
        $pdf = $this->generatePdfContent($invoice);
        
        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice-' . $invoice->invoice_number . '.pdf"',
        ]);
    }
    
    /**
     * Generate simple PDF content for invoice.
     */
    private function generatePdfContent($invoice)
    {
        // For now, return a simple text representation
        // In production, you would use a PDF library like DomPDF or TCPDF
        $content = "INVOICE\n\n";
        $content .= "Invoice Number: {$invoice->invoice_number}\n";
        $content .= "Date: {$invoice->created_at->format('F d, Y')}\n";
        $content .= "Client: {$invoice->client->first_name} {$invoice->client->last_name}\n";
        $content .= "Status: " . ucfirst($invoice->status) . "\n\n";
        
        $content .= "ITEMS:\n";
        $content .= str_repeat('-', 50) . "\n";
        
        foreach ($invoice->items as $item) {
            $content .= "{$item->service_name}\n";
            $content .= "Qty: {$item->quantity} x \${$item->unit_price} = \${$item->total_price}\n\n";
        }
        
        $content .= str_repeat('-', 50) . "\n";
        $content .= "Subtotal: \${$invoice->subtotal}\n";
        $content .= "Tax ({$invoice->tax_rate}%): \${$invoice->tax_amount}\n";
        $content .= "TOTAL: \${$invoice->total_amount}\n";
        
        return $content;
    }
}
