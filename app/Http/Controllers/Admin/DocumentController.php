<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Client;
use App\Models\User;
use App\Rules\ValidDocumentFile;
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
        $query = Document::with([
            'client:id,phone,status,user_id', 
            'client.user:id,first_name,last_name,email', 
            'uploader:id,first_name,last_name,email'
        ]);

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by user_id (since documents store user_id as client_id)
        if ($request->filled('user_id')) {
            $query->where('client_id', $request->user_id);
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
                  ->orWhereHas('client.user', function ($userQuery) use ($search) {
                      $userQuery->whereNotNull('first_name')
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options with enhanced client information
        // Since documents use user_id as client_id, we need to map accordingly
        $clients = Client::with(['user:id,first_name,last_name,email'])
            ->whereHas('user', function ($query) {
                $query->where('role', 'client');
            })
            ->get()
            ->groupBy('user_id') // Group by user_id to handle duplicates
            ->map(function ($clientGroup) {
                // Take the first (or most recent) client for each user
                $client = $clientGroup->sortByDesc('created_at')->first();
                // Get document count using user_id since documents store user_id as client_id
                $documentsCount = Document::where('client_id', $client->user_id)->count();
                
                return [
                    'id' => $client->user_id, // Use user_id instead of client id
                    'first_name' => $client->user?->first_name ?? '',
                    'last_name' => $client->user?->last_name ?? '',
                    'email' => $client->user?->email ?? '',
                    'status' => $client->status,
                    'documents_count' => $documentsCount,
                    'name' => trim(($client->user?->first_name ?? '') . ' ' . ($client->user?->last_name ?? ''))
                ];
            })
            ->sortBy('first_name')
            ->values();

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
        $document->load([
            'client:id,phone,status,user_id', 
            'client.user:id,first_name,last_name,email', 
            'uploader:id,first_name,last_name,email'
        ]);

        // Ensure client and user exist before rendering
        if (!$document->client || !$document->client->user) {
            return back()->with('error', 'Document client information is incomplete.');
        }

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
            // Load the client and user relationships if not already loaded
            $document->load(['client.user']);
            
            $clientUser = $document->client?->user;
            
            if ($clientUser && ($clientUser->first_name || $clientUser->last_name || $clientUser->email)) {
                try {
                    if ($request->status === 'approved') {
                        $clientUser->notify(new DocumentApprovedNotification($document));
                    } elseif ($request->status === 'rejected') {
                        $clientUser->notify(new DocumentRejectedNotification($document));
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to send document status notification', [
                        'document_id' => $document->id,
                        'user_id' => $clientUser->id,
                        'status' => $request->status,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return back()->with('success', 'Document status updated successfully.');
    }

    /**
     * Download or view a document
     */
    public function download(Document $document, Request $request)
    {
        // Documents are stored in private storage for security
        $filePath = storage_path('app/private/' . $document->file_path);
        
        // Fallback to public storage if not found in private
        if (!file_exists($filePath)) {
            $filePath = storage_path('app/public/' . $document->file_path);
        }
        
        if (!file_exists($filePath)) {
            \Log::error('Document file not found', [
                'document_id' => $document->id,
                'file_path' => $document->file_path,
                'attempted_paths' => [
                    storage_path('app/private/' . $document->file_path),
                    storage_path('app/public/' . $document->file_path)
                ]
            ]);
            abort(404, 'File not found');
        }

        // Get the actual MIME type from the file
        $actualMimeType = mime_content_type($filePath);
        $fileExtension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
        
        // Ensure we have a proper filename with extension
        $filename = $document->original_name;
        if (!pathinfo($filename, PATHINFO_EXTENSION) && $fileExtension) {
            $filename .= '.' . $fileExtension;
        }
        
        // Encode filename for proper header handling
        $encodedFilename = rawurlencode($filename);
        
        // Check if it's a PDF and should be viewed inline
        $isPdf = $actualMimeType === 'application/pdf' || 
                 $document->mime_type === 'application/pdf' || 
                 $fileExtension === 'pdf';
        
        if ($isPdf && !$request->has('download')) {
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"; filename*=UTF-8\'\'' . $encodedFilename,
                'Content-Length' => filesize($filePath),
                'Accept-Ranges' => 'bytes',
                'X-Frame-Options' => 'SAMEORIGIN',
                'Cache-Control' => 'public, max-age=3600'
            ]);
        }

        // For downloads, ensure proper headers
        $headers = [
            'Content-Type' => $actualMimeType ?: $document->mime_type ?: 'application/octet-stream',
            'Content-Length' => filesize($filePath),
            'Content-Disposition' => 'attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . $encodedFilename,
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        return response()->download($filePath, $filename, $headers);
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
     * Show documents for a specific client (secure route)
     */
    public function clientDocuments(Request $request, Client $client)
    {
        // Documents are stored with user_id in the client_id column
        $documents = Document::where('client_id', $client->user_id)
            ->with('uploader:id,first_name,last_name,email')
            ->get();
        
        return response()->json(['documents' => $documents]);
    }

    /**
     * Upload documents for a client (admin upload)
     */
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'files.*' => ['required', 'file', 'max:10240', new ValidDocumentFile()],
                'client_id' => 'required|exists:clients,id',
                'document_type' => 'required|string|in:w2,1099,receipts,bank_statements,tax_returns,id_documents,other',
                'tax_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
                'notes' => 'nullable|string|max:500'
            ]);

            $uploadedDocuments = [];
            $client = Client::findOrFail($request->client_id);

            // Ensure the private storage directory exists
            if (!Storage::disk('private')->exists('documents')) {
                Storage::disk('private')->makeDirectory('documents');
            }

            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . uniqid() . '_' . $originalName;
                $filePath = $file->storeAs('documents', $fileName, 'private');

                // Use user_id from the client's relationship instead of client_id
                $userId = $client->user_id ?? $request->client_id;

                $document = Document::create([
                    'client_id' => $userId,
                    'name' => $originalName,
                    'original_name' => $originalName,
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'document_type' => $request->document_type,
                    'tax_year' => $request->tax_year,
                    'notes' => $request->notes,
                    'status' => 'pending',
                    'uploaded_by' => auth()->id()
                ]);

                $uploadedDocuments[] = $document;
            }

            // Send notification to client about new documents
            if ($client && $client->user && !empty($uploadedDocuments)) {
                try {
                    // Ensure user has required properties before sending notification
                    if ($client->user->first_name || $client->user->last_name || $client->user->email) {
                        foreach ($uploadedDocuments as $document) {
                            $client->user->notify(new \App\Notifications\AdminDocumentUploadedNotification($document));
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to send document upload notification', [
                        'client_id' => $client->id,
                        'user_id' => $client->user->id ?? 'null',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'message' => 'Documents uploaded successfully',
                'documents' => $uploadedDocuments
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Document upload failed', [
                'client_id' => $request->client_id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get client document summary for integration with client profiles
     */
    public function clientDocumentSummary(Client $client)
    {
        $documents = $client->documents()->with('uploader:id,first_name,last_name,email')->get();
        
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