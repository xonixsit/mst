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
use App\Enums\VisaStatus;

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
        // Note: date_of_birth and date_of_entry_us are handled by custom accessors/mutators due to encryption
        'insurance_covered' => 'boolean',
        'visa_status' => VisaStatus::class,
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
        'date_of_entry_us',
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
            'visa_status' => 'nullable|in:citizen,permanent_resident,h1b,f1,j1,l1,other',
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
     * Get the client's full name from user.
     */
    public function getFullNameAttribute(): string
    {
        if (!$this->user) {
            return 'Unknown';
        }

        $nameParts = array_filter([
            $this->user->first_name,
            $this->user->middle_name,
            $this->user->last_name
        ]);

        return implode(' ', $nameParts) ?: 'Unknown';
    }

    /**
     * Get the client's email from user.
     */
    public function getEmailAttribute(): ?string
    {
        return $this->user ? $this->user->email : null;
    }

    /**
     * Get the client's first name from user.
     */
    public function getFirstNameAttribute(): ?string
    {
        return $this->user ? $this->user->first_name : null;
    }

    /**
     * Get the client's last name from user.
     */
    public function getLastNameAttribute(): ?string
    {
        return $this->user ? $this->user->last_name : null;
    }

    /**
     * Get the client's middle name from user.
     */
    public function getMiddleNameAttribute(): ?string
    {
        return $this->user ? $this->user->middle_name : null;
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
        return $query->whereHas('user', function ($userQuery) use ($search) {
            $userQuery->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
        })->orWhere('phone', 'like', "%{$search}%");
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
                return $this->email_notifications_enabled ?? true;
            case 'sms':
                return $this->sms_notifications_enabled ?? false;
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

    /**
     * Get the date of birth attribute.
     */
    public function getDateOfBirthAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        // For API/JSON responses, return the date string in Y-m-d format
        // The encryption trait handles decryption, so we just need to ensure proper format
        try {
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            // If parsing fails, try other common formats
            try {
                $date = \Carbon\Carbon::parse($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    /**
     * Set the date of birth attribute.
     */
    public function setDateOfBirthAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['date_of_birth'] = null;
            return;
        }

        // Convert to string format if it's a date object
        if ($value instanceof \DateTime || $value instanceof \Carbon\Carbon) {
            $value = $value->format('Y-m-d');
        } elseif (is_string($value)) {
            // Validate and normalize the date format
            try {
                $date = \Carbon\Carbon::createFromFormat('Y-m-d', $value);
                if (!$date) {
                    $date = \Carbon\Carbon::parse($value);
                }
                $value = $date->format('Y-m-d');
            } catch (\Exception $e) {
                // If date parsing fails, set to null to avoid database errors
                \Log::warning('Invalid date_of_birth format', [
                    'value' => $value,
                    'client_id' => $this->id ?? 'new',
                    'error' => $e->getMessage()
                ]);
                $this->attributes['date_of_birth'] = null;
                return;
            }
        }

        // Store the raw value - encryption will be handled by the trait
        $this->attributes['date_of_birth'] = $value;
    }

    /**
     * Get the date of entry US attribute.
     */
    public function getDateOfEntryUsAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        // For API/JSON responses, return the date string in Y-m-d format
        // The encryption trait handles decryption, so we just need to ensure proper format
        try {
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            // If parsing fails, try other common formats
            try {
                $date = \Carbon\Carbon::parse($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    /**
     * Set the date of entry US attribute.
     */
    public function setDateOfEntryUsAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['date_of_entry_us'] = null;
            return;
        }

        // Convert to string format if it's a date object
        if ($value instanceof \DateTime || $value instanceof \Carbon\Carbon) {
            $value = $value->format('Y-m-d');
        } elseif (is_string($value)) {
            // Validate and normalize the date format
            try {
                $date = \Carbon\Carbon::createFromFormat('Y-m-d', $value);
                if (!$date) {
                    $date = \Carbon\Carbon::parse($value);
                }
                $value = $date->format('Y-m-d');
            } catch (\Exception $e) {
                // If date parsing fails, set to null to avoid database errors
                \Log::warning('Invalid date_of_entry_us format', [
                    'value' => $value,
                    'client_id' => $this->id ?? 'new',
                    'error' => $e->getMessage()
                ]);
                $this->attributes['date_of_entry_us'] = null;
                return;
            }
        }

        // Store the raw value - encryption will be handled by the trait
        $this->attributes['date_of_entry_us'] = $value;
    }

    /**
     * Check if a value is encrypted (helper method for accessors).
     */
    private function isEncrypted($value): bool
    {
        if (!is_string($value)) {
            return false;
        }
        
        return strpos($value, 'eyJpdiI6') === 0 || 
               (strlen($value) > 100 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $value));
    }
}