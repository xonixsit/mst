<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvoiceService
{
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $invoice = Invoice::create([
                'client_id' => $data['client_id'],
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'title' => $data['title'],
                'comments' => $data['comments'] ?? null,
                'send_to_email' => $data['send_to_email'],
                'invoice_year' => $data['invoice_year'],
                'tax_rate' => 18.00,
            ]);

            foreach ($data['items'] as $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_name' => $itemData['service_name'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                ]);
            }

            $invoice->load('items');
            $invoice->calculateTotals();
            $invoice->save();

            return $invoice;
        });
    }

    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice->update([
                'client_id' => $data['client_id'],
                'title' => $data['title'],
                'comments' => $data['comments'] ?? null,
                'send_to_email' => $data['send_to_email'],
                'invoice_year' => $data['invoice_year'],
            ]);

            // Delete existing items and create new ones
            $invoice->items()->delete();

            foreach ($data['items'] as $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_name' => $itemData['service_name'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                ]);
            }

            $invoice->load('items');
            $invoice->calculateTotals();
            $invoice->save();

            return $invoice;
        });
    }

    public function sendInvoice(Invoice $invoice): bool
    {
        try {
            // Here you would implement email sending logic
            // For now, we'll just mark it as sent
            $invoice->markAsSent();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getInvoicesByClient(int $clientId)
    {
        return Invoice::with(['items', 'client'])
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getInvoiceStats()
    {
        return [
            'total_invoices' => Invoice::count(),
            'paid_invoices' => Invoice::where('status', 'paid')->count(),
            'pending_invoices' => Invoice::whereIn('status', ['draft', 'sent'])->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
            'pending_amount' => Invoice::whereIn('status', ['sent', 'overdue'])->sum('total_amount'),
        ];
    }

    public function getDefaultServices(): array
    {
        return [
            ['name' => 'Federal Filing', 'price' => 150.00],
            ['name' => 'State Filing', 'price' => 75.00],
            ['name' => 'City Filing', 'price' => 50.00],
            ['name' => 'County Filing', 'price' => 40.00],
            ['name' => 'Credit', 'price' => 0.00],
            ['name' => 'Sch E Planning', 'price' => 100.00],
            ['name' => 'Sch C Planning', 'price' => 125.00],
            ['name' => 'Stock Transaction', 'price' => 25.00],
            ['name' => 'ITIN Application', 'price' => 200.00],
            ['name' => 'Postal Charges', 'price' => 15.00],
            ['name' => 'Discount', 'price' => 0.00],
        ];
    }

    public function getClientFinancialSummary($client): array
    {
        $invoices = $client->invoices;
        $totalInvoiced = $invoices->sum('total_amount');
        $totalPaid = $invoices->where('status', 'paid')->sum('total_amount');
        $totalOutstanding = $invoices->whereIn('status', ['sent', 'draft', 'overdue'])->sum('total_amount');
        
        $recentInvoices = $invoices->sortByDesc('created_at')->take(5)->values();
        
        return [
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'total_outstanding' => $totalOutstanding,
            'invoice_count' => $invoices->count(),
            'recent_invoices' => $recentInvoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'title' => $invoice->title,
                    'total_amount' => $invoice->total_amount,
                    'status' => $invoice->status,
                    'created_at' => $invoice->created_at,
                ];
            }),
            'payment_history' => $invoices->where('status', 'paid')->map(function ($invoice) {
                return [
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => $invoice->total_amount,
                    'paid_at' => $invoice->paid_at,
                ];
            })->values(),
        ];
    }
}