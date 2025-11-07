<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Document extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'client_id',
        'name',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'document_type',
        'tax_year',
        'status',
        'uploaded_by',
        'notes',
        'archived_at'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'tax_year' => 'integer',
        'uploaded_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get the client that owns the document.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Append formatted file size to array representation.
     */
    protected $appends = ['formatted_file_size'];

    /**
     * Get document type label.
     */
    public function getDocumentTypeLabel(): string
    {
        $types = [
            'w2' => 'W-2 Form',
            '1099' => '1099 Form',
            'receipts' => 'Receipts',
            'bank_statements' => 'Bank Statements',
            'tax_returns' => 'Tax Returns',
            'id_documents' => 'ID Documents',
            'other' => 'Other'
        ];

        return $types[$this->document_type] ?? 'Unknown';
    }

    /**
     * Scope for filtering by document type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope for filtering by tax year.
     */
    public function scopeForTaxYear($query, $year)
    {
        return $query->where('tax_year', $year);
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}