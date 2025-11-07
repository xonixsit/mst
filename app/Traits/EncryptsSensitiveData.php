<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait EncryptsSensitiveData
{
    /**
     * The attributes that should be encrypted.
     *
     * @var array
     */
    protected $encrypted = [];

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
                    $this->attributes[$attribute] = Crypt::encryptString($this->attributes[$attribute]);
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
                        $this->attributes[$attribute] = Crypt::decryptString($this->attributes[$attribute]);
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
        return property_exists($this, 'encrypted') ? $this->encrypted : [];
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
}