<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidDocumentFile implements Rule
{
    private $allowedMimes = [
        'pdf' => 'application/pdf',
        'jpg' => ['image/jpeg', 'image/jpg'],
        'jpeg' => ['image/jpeg', 'image/jpg'],
        'png' => 'image/png',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'webp' => 'image/webp',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'txt' => 'text/plain',
    ];

    public function passes($attribute, $value)
    {
        if (!$value || !is_object($value)) {
            return false;
        }

        $extension = strtolower($value->getClientOriginalExtension());
        $mimeType = $value->getMimeType();

        // Check if extension is allowed
        if (!isset($this->allowedMimes[$extension])) {
            return false;
        }

        // Get allowed MIME types for this extension
        $allowedMimes = $this->allowedMimes[$extension];
        if (!is_array($allowedMimes)) {
            $allowedMimes = [$allowedMimes];
        }

        // For images, be more lenient with MIME type detection
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
            // Check if it's an image type
            return strpos($mimeType, 'image/') === 0;
        }

        // For other files, check exact MIME type
        return in_array($mimeType, $allowedMimes);
    }

    public function message()
    {
        return 'The file format is not supported. Allowed formats: PDF, JPG, JPEG, PNG, GIF, BMP, WEBP, DOC, DOCX, XLS, XLSX, TXT';
    }
}
