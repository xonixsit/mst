<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Client;
use App\Models\User;
use App\Notifications\DocumentApprovedNotification;
use App\Notifications\DocumentRejectedNotification;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function __construct(
        private AdminNotificationService $adminNotificationService
    ) {}

    public function index(Request $request)
    {
        $query = Document::with(['client:id,first_name,last_name,email,phone,status', 'uploader:id,name']);

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by document type
        if ($request->filled('type')) {
            $query->where('document_type', $request->type);
        }

        // Filter by tax year
        if ($request->filled('year')) {
            $query->where('tax_year', $request->year);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by document name or client name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($clientQuery) use ($search) {
                      $clientQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options with enhanced client information
        $clients = Client::select('id', 'first_name', 'last_name', 'email', 'status')
            ->withCount('documents')
            ->orderBy('first_name')
            ->get();

        $taxYears = Document::whereNotNull('tax_year')
            ->distinct()
            ->pluck('tax_year')
            ->sort()
            ->values();

        // Get document statistics by client
        $documentStats = $this->getDocumentStatistics($request);

        return Inertia::render('Admin/Documents', [
            'documents' => $documents,
            'filters' => $request->only(['client_id', 'type', 'year', 'status', 'search']),
            'clients' => $clients,
            'taxYears' => $taxYears,
            'documentStats' => $documentStats,
            'documentTypes' => [
                'w2' => 'W-2 Forms',
                '1099' => '1099 Forms',
                'receipts' => 'Receipts',
                'bank_statements' => 'Bank Statements',
                'tax_returns' => 'Tax Returns',
                'id_documents' => 'ID Documents',
                'other' => 'Other'
            ]
        ]);
    }

    public function show(Document $document)
    {
        $document->load(['client:id,first_name,last_name,email', 'uploader:id,name']);

        return Inertia::render('Admin/DocumentDetail', [
            'document' => $document
        ]);
    }

    public function updateStatus(Request $request, Document $document)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string|max:500'
        ]);

        $oldStatus = $document->status;
        
        $document->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        // Send notification to client if status changed
        if ($oldStatus !== $request->status) {
            $clientUser = User::where('email', $document->client->email)->first();
            
            if ($clientUser) {
                if ($request->status === 'approved') {
                    $clientUser->notify(new DocumentApprovedNotification($document));
                } elseif ($request->status === 'rejected') {
                    $clientUser->notify(new DocumentRejectedNotification($document));
                }
            }
        }

        return back()->with('success', 'Document status updated successfully.');
    }

    public function destroy(Document $document)
    {
        // Delete the physical file
        if (Storage::disk('private')->exists($document->file_path)) {
            Storage::disk('private')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }

    /**
     * Get document statistics for dashboard and reporting
     */
    private function getDocumentStatistics(Request $request): array
    {
        $query = Document::query();
        
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $stats = [
            'total_documents' => $query->count(),
            'pending_documents' => $query->where('status', 'pending')->count(),
            'approved_documents' => $query->where('status', 'approved')->count(),
            'rejected_documents' => $query->where('status', 'rejected')->count(),
            'documents_by_type' => Document::selectRaw('document_type, COUNT(*) as count')
                ->when($request->filled('client_id'), function ($q) use ($request) {
                    return $q->where('client_id', $request->client_id);
                })
                ->groupBy('document_type')
                ->pluck('count', 'document_type'),
            'documents_by_year' => Document::selectRaw('tax_year, COUNT(*) as count')
                ->whereNotNull('tax_year')
                ->when($request->filled('client_id'), function ($q) use ($request) {
                    return $q->where('client_id', $request->client_id);
                })
                ->groupBy('tax_year')
                ->orderBy('tax_year', 'desc')
                ->pluck('count', 'tax_year'),
        ];

        return $stats;
    }

    /**
     * Get client document summary for integration with client profiles
     */
    public function clientDocumentSummary(Client $client)
    {
        $documents = $client->documents()->with('uploader:id,name')->get();
        
        $summary = [
            'total_documents' => $documents->count(),
            'documents_by_status' => $documents->groupBy('status')->map->count(),
            'documents_by_type' => $documents->groupBy('document_type')->map->count(),
            'documents_by_year' => $documents->whereNotNull('tax_year')->groupBy('tax_year')->map->count(),
            'recent_documents' => $documents->sortByDesc('created_at')->take(5)->values(),
            'missing_document_types' => $this->getMissingDocumentTypes($client),
            'document_completion_percentage' => $this->calculateDocumentCompletionPercentage($client),
        ];

        return response()->json(['document_summary' => $summary]);
    }

    /**
     * Identify missing document types for a client based on their profile
     */
    private function getMissingDocumentTypes(Client $client): array
    {
        $requiredTypes = ['w2', '1099', 'id_documents'];
        $existingTypes = $client->documents->pluck('document_type')->unique()->toArray();
        
        // Add conditional requirements based on client information
        if ($client->marital_status === 'married') {
            $requiredTypes[] = 'spouse_documents';
        }
        
        if ($client->assets->count() > 0) {
            $requiredTypes[] = 'receipts';
        }
        
        if ($client->expenses->where('category', 'business')->count() > 0) {
            $requiredTypes[] = 'bank_statements';
        }

        return array_diff($requiredTypes, $existingTypes);
    }

    /**
     * Calculate document completion percentage based on client profile
     */
    private function calculateDocumentCompletionPercentage(Client $client): float
    {
        $requiredDocuments = $this->getRequiredDocumentCount($client);
        $submittedDocuments = $client->documents->count();
        
        if ($requiredDocuments === 0) {
            return 100.0;
        }
        
        return min(100.0, ($submittedDocuments / $requiredDocuments) * 100);
    }

    /**
     * Get required document count based on client profile
     */
    private function getRequiredDocumentCount(Client $client): int
    {
        $baseRequirements = 3; // Basic documents (ID, W2/1099, tax returns)
        
        // Add requirements based on client profile
        if ($client->marital_status === 'married') {
            $baseRequirements += 2; // Spouse documents
        }
        
        if ($client->assets->count() > 0) {
            $baseRequirements += $client->assets->count(); // Asset documentation
        }
        
        if ($client->expenses->count() > 0) {
            $baseRequirements += ceil($client->expenses->count() / 3); // Expense receipts
        }

        return $baseRequirements;
    }
}