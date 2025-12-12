<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Client;
use App\Rules\ValidDocumentFile;
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
        $user = auth()->user();
        
        // Use user ID as client_id for documents
        $clientId = $user->id;

        $query = Document::where('client_id', $clientId)
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
        $taxYears = Document::where('client_id', $clientId)
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
                'bank_statement' => 'Bank Statement',
                'tax_returns' => 'Tax Returns',
                'id_documents' => 'ID Documents',
                'receipt' => 'Receipt',
                'invoice' => 'Invoice',
                'contract' => 'Contract',
                'identification' => 'Identification',
                'other' => 'Other'
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Use user ID as client_id for documents
        $clientId = $user->id;

        $request->validate([
            'file' => ['required', 'file', 'max:10240', new ValidDocumentFile()],
            'document_type' => 'required|in:w2,1099,receipts,bank_statements,tax_returns,id_documents,bank_statement,receipt,invoice,contract,identification,other',
            'tax_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'notes' => 'nullable|string|max:500'
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents/' . $clientId, $fileName, 'private');

        $document = Document::create([
            'client_id' => $clientId,
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

    public function download(Document $document, Request $request)
    {
        $user = auth()->user();
        
        // Use user ID as client_id for documents
        if ($document->client_id !== $user->id) {
            abort(403, 'Unauthorized access to document.');
        }

        if (!Storage::disk('private')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }

        $filePath = storage_path('app/private/' . $document->file_path);
        $mimeType = $document->mime_type ?: mime_content_type($filePath);
        
        // Check if viewing inline (not downloading)
        if ($request->has('view')) {
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $document->original_name . '"'
            ]);
        }

        return Storage::disk('private')->download(
            $document->file_path,
            $document->original_name
        );
    }

    public function destroy(Document $document)
    {
        $user = auth()->user();
        
        // Use user ID as client_id for documents
        if ($document->client_id !== $user->id) {
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

    /**
     * Initialize chunked upload session
     */
    public function initChunkedUpload(Request $request)
    {
        $user = auth()->user();
        $clientId = $user->id;

        $request->validate([
            'file_name' => 'required|string',
            'file_size' => 'required|integer|max:26214400', // 25MB
            'file_type' => 'required|string',
            'file_id' => 'required|string',
            'total_chunks' => 'required|integer|min:1',
            'document_type' => 'required|string|in:w2,1099,receipts,bank_statements,tax_returns,id_documents,other',
            'tax_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'notes' => 'nullable|string|max:500'
        ]);

        $uploadSessionId = uniqid('upload_', true);
        
        // Store upload session metadata in cache
        \Cache::put("upload_session_{$uploadSessionId}", [
            'file_name' => $request->file_name,
            'file_size' => $request->file_size,
            'file_type' => $request->file_type,
            'file_id' => $request->file_id,
            'total_chunks' => $request->total_chunks,
            'client_id' => $clientId,
            'document_type' => $request->document_type,
            'tax_year' => $request->tax_year,
            'notes' => $request->notes,
            'chunks_received' => 0,
            'created_at' => now()
        ], 3600); // 1 hour expiry

        return response()->json(['upload_session_id' => $uploadSessionId]);
    }

    /**
     * Upload a single chunk
     */
    public function uploadChunk(Request $request)
    {
        $request->validate([
            'chunk' => 'required|file|max:10240', // 10MB per chunk
            'chunk_index' => 'required|integer|min:0',
            'total_chunks' => 'required|integer|min:1',
            'upload_session_id' => 'required|string',
            'file_id' => 'required|string'
        ]);

        $uploadSessionId = $request->upload_session_id;
        $sessionData = \Cache::get("upload_session_{$uploadSessionId}");

        if (!$sessionData) {
            return response()->json(['error' => 'Upload session expired'], 410);
        }

        // Verify client ownership
        if ($sessionData['client_id'] !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Create temp directory for chunks
        $tempDir = storage_path("app/private/uploads/{$uploadSessionId}");
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Store chunk
        $chunkPath = "{$tempDir}/chunk_{$request->chunk_index}";
        $request->file('chunk')->move($tempDir, "chunk_{$request->chunk_index}");

        // Update session
        $sessionData['chunks_received']++;
        \Cache::put("upload_session_{$uploadSessionId}", $sessionData, 3600);

        return response()->json([
            'chunk_index' => $request->chunk_index,
            'chunks_received' => $sessionData['chunks_received'],
            'total_chunks' => $request->total_chunks
        ]);
    }

    /**
     * Finalize chunked upload and assemble file
     */
    public function finalizeChunkedUpload(Request $request)
    {
        $user = auth()->user();
        $clientId = $user->id;

        $request->validate([
            'upload_session_id' => 'required|string',
            'file_id' => 'required|string'
        ]);

        $uploadSessionId = $request->upload_session_id;
        $sessionData = \Cache::get("upload_session_{$uploadSessionId}");

        if (!$sessionData) {
            return response()->json(['error' => 'Upload session expired'], 410);
        }

        // Verify client ownership
        if ($sessionData['client_id'] !== $clientId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($sessionData['chunks_received'] !== $sessionData['total_chunks']) {
            return response()->json([
                'error' => 'Not all chunks received',
                'chunks_received' => $sessionData['chunks_received'],
                'total_chunks' => $sessionData['total_chunks']
            ], 400);
        }

        try {
            $tempDir = storage_path("app/private/uploads/{$uploadSessionId}");
            $fileName = time() . '_' . uniqid() . '_' . $sessionData['file_name'];
            $finalPath = "documents/{$clientId}/{$fileName}";
            $finalFilePath = storage_path("app/private/{$finalPath}");

            // Ensure documents directory exists
            $docDir = storage_path("app/private/documents/{$clientId}");
            if (!is_dir($docDir)) {
                mkdir($docDir, 0755, true);
            }

            // Assemble chunks
            $outputFile = fopen($finalFilePath, 'wb');
            for ($i = 0; $i < $sessionData['total_chunks']; $i++) {
                $chunkFile = "{$tempDir}/chunk_{$i}";
                if (file_exists($chunkFile)) {
                    $chunkData = fopen($chunkFile, 'rb');
                    stream_copy_to_stream($chunkData, $outputFile);
                    fclose($chunkData);
                    unlink($chunkFile);
                }
            }
            fclose($outputFile);

            // Clean up temp directory
            rmdir($tempDir);

            // Create document record
            $document = Document::create([
                'client_id' => $clientId,
                'name' => $sessionData['file_name'],
                'original_name' => $sessionData['file_name'],
                'file_path' => $finalPath,
                'file_size' => $sessionData['file_size'],
                'mime_type' => $sessionData['file_type'],
                'document_type' => $sessionData['document_type'],
                'tax_year' => $sessionData['tax_year'],
                'notes' => $sessionData['notes'],
                'status' => 'pending',
                'uploaded_by' => auth()->id()
            ]);

            // Notify admins about new document upload
            $this->adminNotificationService->notifyDocumentUploaded($document);

            // Clear cache
            \Cache::forget("upload_session_{$uploadSessionId}");

            return response()->json([
                'message' => 'Document uploaded successfully',
                'document' => $document
            ]);

        } catch (\Exception $e) {
            \Log::error('Chunked upload finalization failed', [
                'upload_session_id' => $uploadSessionId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to finalize upload'], 500);
        }
    }
}