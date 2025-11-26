<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Document;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Reports/Index');
    }

    /**
     * Get quick statistics for dashboard
     */
    public function stats()
    {
        $stats = [
            'total_clients' => Client::count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
            'total_documents' => Document::count(),
            'total_messages' => Message::count(),
        ];

        return response()->json($stats);
    }

    /**
     * Generate client summary report
     */
    public function clientSummary(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'date_range' => 'nullable|in:3months,6months,12months,custom',
            'status' => 'nullable|in:active,inactive,archived',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        // Handle predefined date ranges
        $dates = $this->getDateRange($request);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        $query = Client::with(['user', 'invoices', 'documents']);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $clients = $query->get();

        $data = [
            'title' => 'Client Summary Report',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'filters' => array_merge(
                $request->only(['status', 'date_range']),
                ['start_date' => $startDate, 'end_date' => $endDate]
            ),
            'summary' => [
                'total_clients' => $clients->count(),
                'active_clients' => $clients->where('status', 'active')->count(),
                'inactive_clients' => $clients->where('status', 'inactive')->count(),
                'archived_clients' => $clients->where('status', 'archived')->count(),
                'total_invoices' => $clients->sum(fn($c) => $c->invoices->count()),
                'total_documents' => $clients->sum(fn($c) => $c->documents->count()),
            ],
            'clients' => $clients->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->full_name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'status' => $client->status,
                    'created_at' => $client->created_at->format('Y-m-d'),
                    'invoices_count' => $client->invoices->count(),
                    'documents_count' => $client->documents->count(),
                    'total_invoice_amount' => $client->invoices->sum('total_amount'),
                ];
            })
        ];

        return $this->generateReport($data, $request->format, 'client-summary');
    }

    /**
     * Generate financial report
     */
    public function financial(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'date_range' => 'nullable|in:3months,6months,12months,custom',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        // Handle predefined date ranges
        $dates = $this->getDateRange($request);
        $startDate = $dates['start_date'] ? Carbon::parse($dates['start_date']) : Carbon::now()->startOfYear();
        $endDate = $dates['end_date'] ? Carbon::parse($dates['end_date']) : Carbon::now();

        $invoices = Invoice::with(['client.user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $monthlyData = $invoices->groupBy(function ($invoice) {
            return $invoice->created_at->format('Y-m');
        })->map(function ($monthInvoices) {
            return [
                'total_invoices' => $monthInvoices->count(),
                'total_amount' => $monthInvoices->sum('total_amount'),
                'paid_amount' => $monthInvoices->where('status', 'paid')->sum('total_amount'),
                'pending_amount' => $monthInvoices->where('status', 'pending')->sum('total_amount'),
            ];
        });

        $data = [
            'title' => 'Financial Report',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'filters' => array_merge(
                $request->only(['date_range']),
                ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]
            ),
            'summary' => [
                'total_invoices' => $invoices->count(),
                'total_amount' => $invoices->sum('total_amount'),
                'paid_amount' => $invoices->where('status', 'paid')->sum('total_amount'),
                'pending_amount' => $invoices->where('status', 'pending')->sum('total_amount'),
                'overdue_amount' => $invoices->where('status', 'overdue')->sum('total_amount'),
            ],
            'monthly_breakdown' => $monthlyData,
            'invoices' => $invoices->map(function ($invoice) {
                $clientName = 'N/A';
                if ($invoice->client && $invoice->client->user) {
                    $clientName = trim(($invoice->client->user->first_name ?? '') . ' ' . ($invoice->client->user->last_name ?? ''));
                }
                
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'client_name' => $clientName ?: 'N/A',
                    'amount' => $invoice->total_amount,
                    'status' => $invoice->status,
                    'created_at' => $invoice->created_at->format('Y-m-d'),
                    'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
                ];
            })
        ];

        return $this->generateReport($data, $request->format, 'financial-report');
    }

    /**
     * Generate document activity report
     */
    public function documentActivity(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'date_range' => 'nullable|in:3months,6months,12months,custom',
            'status' => 'nullable|in:pending,approved,rejected',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        // Handle predefined date ranges
        $dates = $this->getDateRange($request);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        $query = Document::with(['client.user']);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $documents = $query->get();

        $typeBreakdown = $documents->groupBy('document_type')->map(function ($docs) {
            return [
                'count' => $docs->count(),
                'pending' => $docs->where('status', 'pending')->count(),
                'approved' => $docs->where('status', 'approved')->count(),
                'rejected' => $docs->where('status', 'rejected')->count(),
            ];
        });

        $data = [
            'title' => 'Document Activity Report',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'filters' => array_merge(
                $request->only(['status', 'date_range']),
                ['start_date' => $startDate, 'end_date' => $endDate]
            ),
            'summary' => [
                'total_documents' => $documents->count(),
                'pending_documents' => $documents->where('status', 'pending')->count(),
                'approved_documents' => $documents->where('status', 'approved')->count(),
                'rejected_documents' => $documents->where('status', 'rejected')->count(),
            ],
            'type_breakdown' => $typeBreakdown,
            'documents' => $documents->map(function ($document) {
                return [
                    'id' => $document->id,
                    'name' => $document->name,
                    'type' => $document->document_type,
                    'client_name' => $document->client->full_name ?? 'N/A',
                    'status' => $document->status,
                    'uploaded_at' => $document->created_at->format('Y-m-d H:i:s'),
                    'file_size' => $document->file_size,
                ];
            })
        ];

        return $this->generateReport($data, $request->format, 'document-activity');
    }

    /**
     * Generate communication report
     */
    public function communication(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'date_range' => 'nullable|in:3months,6months,12months,custom',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        // Handle predefined date ranges
        $dates = $this->getDateRange($request);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        $query = Message::with(['client.user']);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $messages = $query->get();

        $clientActivity = $messages->groupBy('client_id')->map(function ($clientMessages) {
            $client = $clientMessages->first()->client;
            return [
                'client_name' => $client->full_name ?? 'N/A',
                'message_count' => $clientMessages->count(),
                'last_message' => $clientMessages->sortByDesc('created_at')->first()->created_at->format('Y-m-d H:i:s'),
            ];
        })->sortByDesc('message_count');

        $data = [
            'title' => 'Communication Report',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'filters' => array_merge(
                $request->only(['date_range']),
                ['start_date' => $startDate, 'end_date' => $endDate]
            ),
            'summary' => [
                'total_messages' => $messages->count(),
                'unique_clients' => $messages->pluck('client_id')->unique()->count(),
                'avg_messages_per_client' => $messages->count() > 0 ? round($messages->count() / $messages->pluck('client_id')->unique()->count(), 2) : 0,
            ],
            'client_activity' => $clientActivity->values(),
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'client_name' => $message->client->full_name ?? 'N/A',
                    'subject' => $message->subject,
                    'sent_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'is_read' => $message->is_read,
                ];
            })
        ];

        return $this->generateReport($data, $request->format, 'communication-report');
    }

    /**
     * Get date range based on request parameters
     */
    private function getDateRange(Request $request)
    {
        $dateRange = $request->input('date_range');
        
        if ($dateRange === 'custom' || !$dateRange) {
            return [
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date')
            ];
        }
        
        $endDate = Carbon::now();
        $startDate = match($dateRange) {
            '3months' => Carbon::now()->subMonths(3),
            '6months' => Carbon::now()->subMonths(6),
            '12months' => Carbon::now()->subMonths(12),
            default => null
        };
        
        return [
            'start_date' => $startDate ? $startDate->format('Y-m-d') : null,
            'end_date' => $endDate->format('Y-m-d')
        ];
    }

    /**
     * Generate report in specified format
     */
    private function generateReport(array $data, string $format, string $filename)
    {
        switch ($format) {
            case 'pdf':
                return $this->generatePdfReport($data, $filename);
            case 'excel':
                return $this->generateExcelReport($data, $filename);
            case 'csv':
                return $this->generateCsvReport($data, $filename);
            default:
                abort(400, 'Invalid format');
        }
    }

    /**
     * Generate PDF report
     */
    private function generatePdfReport(array $data, string $filename)
    {
        $pdf = Pdf::loadView('reports.pdf-template', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download($filename . '.pdf');
    }

    /**
     * Generate Excel report
     */
    private function generateExcelReport(array $data, string $filename)
    {
        return Excel::download(new ReportExport($data, $data['title']), $filename . '.xlsx');
    }

    /**
     * Generate CSV report
     */
    private function generateCsvReport(array $data, string $filename)
    {
        $csv = "Report: {$data['title']}\n";
        $csv .= "Generated: {$data['generated_at']}\n\n";
        
        // Add summary
        if (isset($data['summary'])) {
            $csv .= "SUMMARY\n";
            foreach ($data['summary'] as $key => $value) {
                $csv .= ucwords(str_replace('_', ' ', $key)) . "," . $value . "\n";
            }
            $csv .= "\n";
        }

        // Add main data
        if (isset($data['clients'])) {
            $csv .= "CLIENT DATA\n";
            $csv .= "ID,Name,Email,Phone,Status,Created,Invoices,Documents,Total Amount\n";
            foreach ($data['clients'] as $client) {
                $csv .= implode(',', [
                    $client['id'],
                    '"' . $client['name'] . '"',
                    $client['email'],
                    $client['phone'],
                    $client['status'],
                    $client['created_at'],
                    $client['invoices_count'],
                    $client['documents_count'],
                    $client['total_invoice_amount']
                ]) . "\n";
            }
        }

        if (isset($data['invoices'])) {
            $csv .= "INVOICE DATA\n";
            $csv .= "ID,Invoice Number,Client,Amount,Status,Created,Due Date\n";
            foreach ($data['invoices'] as $invoice) {
                $csv .= implode(',', [
                    $invoice['id'],
                    $invoice['invoice_number'],
                    '"' . $invoice['client_name'] . '"',
                    $invoice['amount'],
                    $invoice['status'],
                    $invoice['created_at'],
                    $invoice['due_date'] ?? ''
                ]) . "\n";
            }
        }

        if (isset($data['documents'])) {
            $csv .= "DOCUMENT DATA\n";
            $csv .= "ID,Name,Type,Client,Status,Uploaded,File Size\n";
            foreach ($data['documents'] as $document) {
                $csv .= implode(',', [
                    $document['id'],
                    '"' . $document['name'] . '"',
                    $document['type'],
                    '"' . $document['client_name'] . '"',
                    $document['status'],
                    $document['uploaded_at'],
                    $document['file_size']
                ]) . "\n";
            }
        }

        if (isset($data['messages'])) {
            $csv .= "MESSAGE DATA\n";
            $csv .= "ID,Client,Subject,Sent At,Read\n";
            foreach ($data['messages'] as $message) {
                $csv .= implode(',', [
                    $message['id'],
                    '"' . $message['client_name'] . '"',
                    '"' . $message['subject'] . '"',
                    $message['sent_at'],
                    $message['is_read'] ? 'Yes' : 'No'
                ]) . "\n";
            }
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');
    }
}