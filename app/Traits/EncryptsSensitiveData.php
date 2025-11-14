<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

trait EncryptsSensitiveData
{
    /**
     * Boot the trait.
     */
    public static function bootEncryptsSensitiveData()
    {
        static::saving(function ($model) {
            $model->encryptSensitiveAttributes();
        });

        static::retrieved(function ($model) {
            $model->decryptSensitiveAttributes();
        });
    }

    /**
     * Encrypt sensitive attributes before saving.
     */
    protected function encryptSensitiveAttributes()
    {
        foreach ($this->getEncryptedAttributes() as $attribute) {
            if (isset($this->attributes[$attribute]) && !empty($this->attributes[$attribute])) {
                // Only encrypt if not already encrypted
                if (!$this->isEncrypted($this->attributes[$attribute])) {
                    // Convert dates to string format before encryption
                    $value = $this->attributes[$attribute];
                    if ($this->isEncryptedDateAttribute($attribute)) {
                        if ($value instanceof \DateTime || $value instanceof \Carbon\Carbon) {
                            $value = $value->format('Y-m-d');
                        } elseif (is_string($value)) {
                            // Ensure date is in proper format and validate it
                            try {
                                // Try to parse the date to validate it
                                $date = \Carbon\Carbon::createFromFormat('Y-m-d', $value);
                                if (!$date) {
                                    // Try other common formats
                                    $date = \Carbon\Carbon::parse($value);
                                    $value = $date->format('Y-m-d');
                                }
                            } catch (\Exception $e) {
                                // If date parsing fails, don't encrypt invalid dates
                                \Log::warning('Invalid date format for encryption', [
                                    'attribute' => $attribute,
                                    'value' => $value,
                                    'error' => $e->getMessage()
                                ]);
                                continue;
                            }
                        }
                    }
                    
                    $this->attributes[$attribute] = Crypt::encryptString($value);
                }
            }
        }
    }

    /**
     * Decrypt sensitive attributes after retrieval.
     */
    protected function decryptSensitiveAttributes()
    {
        foreach ($this->getEncryptedAttributes() as $attribute) {
            if (isset($this->attributes[$attribute]) && !empty($this->attributes[$attribute])) {
                try {
                    // Only decrypt if it's encrypted
                    if ($this->isEncrypted($this->attributes[$attribute])) {
                        $decryptedValue = Crypt::decryptString($this->attributes[$attribute]);
                        
                        // Convert back to appropriate format for date attributes
                        if ($this->isEncryptedDateAttribute($attribute) && is_string($decryptedValue)) {
                            try {
                                // Keep as string for now, let Laravel's casting handle the conversion
                                $this->attributes[$attribute] = $decryptedValue;
                            } catch (\Exception $e) {
                                $this->attributes[$attribute] = $decryptedValue;
                            }
                        } else {
                            $this->attributes[$attribute] = $decryptedValue;
                        }
                    }
                } catch (\Exception $e) {
                    // If decryption fails, leave the value as is
                    // This handles cases where data might not be encrypted yet
                }
            }
        }
    }

    /**
     * Get the encrypted attributes for this model.
     */
    protected function getEncryptedAttributes(): array
    {
        // Check if the model has an $encrypted property defined
        if (property_exists($this, 'encrypted') && is_array($this->encrypted)) {
            return $this->encrypted;
        }
        
        // Fallback to empty array if no encrypted attributes defined
        return [];
    }

    /**
     * Check if a value is encrypted.
     */
    protected function isEncrypted(string $value): bool
    {
        // Laravel's encrypted values start with 'eyJpdiI6' (base64 encoded JSON)
        return strpos($value, 'eyJpdiI6') === 0 || 
               (strlen($value) > 100 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $value));
    }

    /**
     * Get decrypted value for an encrypted attribute.
     */
    public function getDecryptedAttribute(string $attribute): ?string
    {
        if (!in_array($attribute, $this->getEncryptedAttributes())) {
            return $this->getAttribute($attribute);
        }

        $value = $this->getAttributeFromArray($attribute);
        
        if (empty($value)) {
            return null;
        }

        try {
            return $this->isEncrypted($value) ? Crypt::decryptString($value) : $value;
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Set encrypted value for an attribute.
     */
    public function setEncryptedAttribute(string $attribute, ?string $value): void
    {
        if (!in_array($attribute, $this->getEncryptedAttributes())) {
            $this->setAttribute($attribute, $value);
            return;
        }

        if (empty($value)) {
            $this->attributes[$attribute] = null;
            return;
        }

        $this->attributes[$attribute] = $this->isEncrypted($value) ? $value : Crypt::encryptString($value);
    }

    /**
     * Check if an attribute is an encrypted date attribute that needs special handling.
     */
    protected function isEncryptedDateAttribute(string $attribute): bool
    {
        $dateAttributes = ['date_of_birth', 'date_of_entry_us'];
        return in_array($attribute, $dateAttributes);
    }
}