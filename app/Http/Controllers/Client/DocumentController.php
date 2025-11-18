<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Client;
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
        // For now, create a mock client or use the authenticated user directly
        $user = auth()->user();
        
        // Create a mock client ID based on user ID for testing
        $mockClientId = $user->id;

        $query = Document::where('client_id', $mockClientId)
            ->with('uploader');

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

        $documents = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get available tax years
        $taxYears = Document::where('client_id', $mockClientId)
            ->whereNotNull('tax_year')
            ->distinct()
            ->pluck('tax_year')
            ->sort()
            ->values();

        return Inertia::render('Client/Documents', [
            'documents' => $documents,
            'filters' => $request->only(['type', 'year', 'status']),
            'taxYears' => $taxYears,
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

    public function store(Request $request)
    {
        $user = auth()->user();
        $mockClientId = $user->id;

        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
            'document_type' => 'required|in:w2,1099,receipts,bank_statements,tax_returns,id_documents,other',
            'tax_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'notes' => 'nullable|string|max:500'
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents/' . $mockClientId, $fileName, 'private');

        $document = Document::create([
            'client_id' => $mockClientId,
            'name' => $request->input('name', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'document_type' => $request->document_type,
            'tax_year' => $request->tax_year,
            'uploaded_by' => auth()->id(),
            'notes' => $request->notes
        ]);

        // Notify admins about new document upload
        $this->adminNotificationService->notifyDocumentUploaded($document);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function download(Document $document)
    {
        $user = auth()->user();
        $mockClientId = $user->id;
        
        if ($document->client_id !== $mockClientId) {
            abort(403, 'Unauthorized access to document.');
        }

        if (!Storage::disk('private')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('private')->download(
            $document->file_path,
            $document->original_name
        );
    }

    public function destroy(Document $document)
    {
        $user = auth()->user();
        $mockClientId = $user->id;
        
        if ($document->client_id !== $mockClientId) {
            abort(403, 'Unauthorized access to document.');
        }

        // Only allow deletion if document is pending
        if ($document->status !== 'pending') {
            return back()->with('error', 'Cannot delete processed documents.');
        }

        Storage::disk('private')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }
}