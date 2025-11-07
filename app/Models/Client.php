<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Services\AuditService;
use App\Models\AuditLog;
use App\Traits\Auditable;
use App\Traits\EncryptsSensitiveData;

class Client extends Model
{
    use HasFactory, Auditable, EncryptsSensitiveData;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'ssn',
        'date_of_birth',
        'marital_status',
        'occupation',
        'insurance_covered',
        'street_no',
        'apartment_no',
        'zip_code',
        'city',
        'state',
        'country',
        'mobile_number',
        'work_number',
        'visa_status',
        'date_of_entry_us',
        'status',
        'review_requested_at',
        'review_sections',
        'review_message',
        'review_priority',
        'archived_at',
        'section_completion_status',
        'last_export_at',
        'communication_preferences',
        'last_communication_at',
        'preferred_communication_method',
        'email_notifications_enabled',
        'sms_notifications_enabled',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_entry_us' => 'date',
        'insurance_covered' => 'boolean',
        'review_requested_at' => 'datetime',
        'review_sections' => 'array',
        'section_completion_status' => 'array',
        'last_export_at' => 'datetime',
        'communication_preferences' => 'array',
        'last_communication_at' => 'datetime',
        'email_notifications_enabled' => 'boolean',
        'sms_notifications_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'ssn', // Hide SSN in JSON responses for security
    ];

    /**
     * The attributes that should be encrypted.
     *
     * @var array<int, string>
     */
    protected $encrypted = [
        'ssn',
        'date_of_birth',
        'mobile_number',
        'work_number',
    ];

    /**
     * Validation rules for client data.
     *
     * @return array<string, string>
     */
    public static function validationRules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|regex:/^[\+]?[1-9][\d]{0,15}$/',
            'ssn' => 'nullable|string|regex:/^\d{3}-\d{2}-\d{4}$/',
            'date_of_birth' => 'nullable|date|before:today',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'occupation' => 'nullable|string|max:255',
            'insurance_covered' => 'boolean',
            'street_no' => 'nullable|string|max:255',
            'apartment_no' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|regex:/^[\+]?[1-9][\d]{0,15}$/',
            'work_number' => 'nullable|string|regex:/^[\+]?[1-9][\d]{0,15}$/',
            'visa_status' => 'nullable|string|max:255',
            'date_of_entry_us' => 'nullable|date',
            'status' => 'required|in:active,inactive,archived',
        ];
    }

    /**
     * Get the user that manages this client.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client's spouse information.
     */
    public function spouse(): HasOne
    {
        return $this->hasOne(ClientSpouse::class);
    }

    /**
     * Get the client's employment information.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(ClientEmployee::class);
    }

    /**
     * Get the client's projects.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(ClientProject::class);
    }

    /**
     * Get the client's assets.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(ClientAsset::class);
    }

    /**
     * Get the client's expenses.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(ClientExpense::class);
    }

    /**
     * Get the client's documents.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the client's messages.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the client's invoices.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the client's full name.
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        
        return $name;
    }

    /**
     * Get the client's formatted address.
     */
    public function getFormattedAddressAttribute(): string
    {
        $address = $this->street_no;
        if ($this->apartment_no) {
            $address .= ', ' . $this->apartment_no;
        }
        $address .= ', ' . $this->city . ', ' . $this->state . ' ' . $this->zip_code;
        if ($this->country !== 'USA') {
            $address .= ', ' . $this->country;
        }
        
        return $address;
    }

    /**
     * Scope a query to only include active clients.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to search clients by name or email.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
        });
    }

    /**
     * Get all audit logs for this client.
     */
    // Audit functionality is now handled by the Auditable trait

    /**
     * Get client communication preferences with defaults
     */
    public function getCommunicationPreferences(): array
    {
        $defaults = [
            'email_notifications' => true,
            'document_notifications' => true,
            'invoice_notifications' => true,
            'reminder_notifications' => true,
            'notification_frequency' => 'immediate',
        ];

        return array_merge($defaults, $this->communication_preferences ?? []);
    }

    /**
     * Update communication preferences
     */
    public function updateCommunicationPreferences(array $preferences): void
    {
        $this->update([
            'communication_preferences' => array_merge($this->getCommunicationPreferences(), $preferences),
            'last_communication_at' => now(),
        ]);
    }

    /**
     * Check if client should receive notifications
     */
    public function shouldReceiveNotification(string $type): bool
    {
        $preferences = $this->getCommunicationPreferences();
        
        switch ($type) {
            case 'email':
                return $this->email_notifications_enabled;
            case 'sms':
                return $this->sms_notifications_enabled;
            case 'document':
                return $preferences['document_notifications'] ?? true;
            case 'invoice':
                return $preferences['invoice_notifications'] ?? true;
            case 'reminder':
                return $preferences['reminder_notifications'] ?? true;
            default:
                return true;
        }
    }
}