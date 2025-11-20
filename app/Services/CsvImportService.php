<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use App\Services\ClientInformationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use Exception;

class CsvImportService
{
    public function __construct(
        private ClientInformationService $clientService
    ) {}

    /**
     * Import clients from CSV file with validation and error reporting.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return array
     */
    public function importClientsFromCsv(UploadedFile $file, array $options = []): array
    {
        try {
            // Validate file
            $this->validateCsvFile($file);

            // Parse CSV content
            $csvData = $this->parseCsvFile($file);

            // Validate CSV structure
            $this->validateCsvStructure($csvData);

            // Process import with preview option
            if ($options['preview'] ?? false) {
                return $this->previewImport($csvData, $options);
            }

            return $this->processImport($csvData, $options);

        } catch (Exception $e) {
            Log::error('CSV import failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            
            return [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'imported' => 0,
                'errors' => [],
                'skipped' => 0
            ];
        }
    }

    /**
     * Validate the uploaded CSV file.
     *
     * @param UploadedFile $file
     * @throws Exception
     */
    private function validateCsvFile(UploadedFile $file): void
    {
        // Check file extension
        $allowedExtensions = ['csv', 'txt'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Invalid file type. Only CSV files are allowed.');
        }

        // Check file size (max 10MB)
        $maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if ($file->getSize() > $maxSize) {
            throw new Exception('File size exceeds maximum limit of 10MB.');
        }

        // Check if file is readable
        if (!$file->isValid()) {
            throw new Exception('Invalid or corrupted file.');
        }
    }

    /**
     * Parse CSV file content.
     *
     * @param UploadedFile $file
     * @return array
     * @throws Exception
     */
    public function parseCsvFile(UploadedFile $file): array
    {
        $csvData = [];
        $handle = fopen($file->getPathname(), 'r');

        if ($handle === false) {
            throw new Exception('Unable to read CSV file.');
        }

        try {
            // Read header row
            $headers = fgetcsv($handle);
            if ($headers === false || empty($headers)) {
                throw new Exception('CSV file appears to be empty or invalid.');
            }

            // Clean and normalize headers
            $headers = array_map(function($header) {
                return trim(strtolower(str_replace(' ', '_', $header)));
            }, $headers);

            $rowNumber = 1;
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Ensure row has same number of columns as headers
                if (count($row) !== count($headers)) {
                    Log::warning("Row {$rowNumber} has mismatched column count", [
                        'expected' => count($headers),
                        'actual' => count($row)
                    ]);
                    // Pad or trim row to match headers
                    $row = array_pad(array_slice($row, 0, count($headers)), count($headers), '');
                }

                $csvData[] = [
                    'row_number' => $rowNumber,
                    'data' => array_combine($headers, $row)
                ];
            }

            return $csvData;

        } finally {
            fclose($handle);
        }
    }

    /**
     * Validate CSV structure and required columns.
     *
     * @param array $csvData
     * @throws Exception
     */
    private function validateCsvStructure(array $csvData): void
    {
        if (empty($csvData)) {
            throw new Exception('CSV file contains no data rows.');
        }

        // Required columns for client import
        $requiredColumns = ['first_name', 'last_name', 'email'];
        $firstRow = $csvData[0]['data'];
        $availableColumns = array_keys($firstRow);

        $missingColumns = array_diff($requiredColumns, $availableColumns);
        if (!empty($missingColumns)) {
            throw new Exception('Missing required columns: ' . implode(', ', $missingColumns));
        }
    }

    /**
     * Preview import data without actually importing.
     *
     * @param array $csvData
     * @param array $options
     * @return array
     */
    private function previewImport(array $csvData, array $options = []): array
    {
        $preview = [];
        $validRows = 0;
        $invalidRows = 0;
        $errors = [];

        foreach ($csvData as $rowData) {
            $rowNumber = $rowData['row_number'];
            $data = $rowData['data'];

            // Transform and validate row data
            $clientData = $this->transformCsvRowToClientData($data);
            $validation = $this->validateClientData($clientData, $rowNumber);

            $preview[] = [
                'row_number' => $rowNumber,
                'data' => $clientData,
                'is_valid' => $validation['is_valid'],
                'errors' => $validation['errors'],
                'warnings' => $validation['warnings'] ?? []
            ];

            if ($validation['is_valid']) {
                $validRows++;
            } else {
                $invalidRows++;
                $errors = array_merge($errors, $validation['errors']);
            }
        }

        return [
            'success' => true,
            'preview' => true,
            'total_rows' => count($csvData),
            'valid_rows' => $validRows,
            'invalid_rows' => $invalidRows,
            'preview_data' => array_slice($preview, 0, 10), // Show first 10 rows for preview
            'sample_headers' => $this->getSampleHeaders(),
            'errors' => $errors
        ];
    }

    /**
     * Process the actual import.
     *
     * @param array $csvData
     * @param array $options
     * @return array
     */
    private function processImport(array $csvData, array $options = []): array
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];
        $skipInvalid = $options['skip_invalid'] ?? true;
        $updateExisting = $options['update_existing'] ?? false;

        DB::beginTransaction();

        try {
            foreach ($csvData as $rowData) {
                $rowNumber = $rowData['row_number'];
                $data = $rowData['data'];

                try {
                    // Transform and validate row data
                    $clientData = $this->transformCsvRowToClientData($data);
                    $validation = $this->validateClientData($clientData, $rowNumber);

                    if (!$validation['is_valid']) {
                        if ($skipInvalid) {
                            $skipped++;
                            $errors = array_merge($errors, $validation['errors']);
                            continue;
                        } else {
                            throw new Exception("Row {$rowNumber}: " . implode(', ', $validation['errors']));
                        }
                    }

                    // Check for existing client by email
                    $existingClient = Client::whereHas('user', function ($query) use ($clientData) {
                        $query->where('email', $clientData['email']);
                    })->first();

                    if ($existingClient) {
                        if ($updateExisting) {
                            $this->clientService->updateClient($existingClient->id, $clientData);
                            $imported++;
                        } else {
                            $skipped++;
                            $errors[] = "Row {$rowNumber}: Client with email {$clientData['email']} already exists";
                        }
                    } else {
                        // Set default values
                        $clientData['status'] = $clientData['status'] ?? 'active';
                        $clientData['user_id'] = $options['assign_to_user'] ?? null;

                        $this->clientService->createClient($clientData);
                        $imported++;
                    }

                } catch (Exception $e) {
                    $errorMessage = "Row {$rowNumber}: " . $e->getMessage();
                    $errors[] = $errorMessage;
                    
                    if (!$skipInvalid) {
                        throw new Exception($errorMessage);
                    }
                    
                    $skipped++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Import completed. {$imported} clients imported, {$skipped} skipped.",
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
                'total_processed' => count($csvData)
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Transform CSV row data to client data format.
     *
     * @param array $row
     * @return array
     */
    private function transformCsvRowToClientData(array $row): array
    {
        // Map CSV columns to client fields
        $fieldMapping = [
            'first_name' => 'first_name',
            'middle_name' => 'middle_name',
            'last_name' => 'last_name',
            'email' => 'email',
            'phone' => 'phone',
            'mobile_number' => 'mobile_number',
            'work_number' => 'work_number',
            'ssn' => 'ssn',
            'date_of_birth' => 'date_of_birth',
            'marital_status' => 'marital_status',
            'occupation' => 'occupation',
            'insurance_covered' => 'insurance_covered',
            'street_no' => 'street_no',
            'apartment_no' => 'apartment_no',
            'zip_code' => 'zip_code',
            'city' => 'city',
            'state' => 'state',
            'country' => 'country',
            'visa_status' => 'visa_status',
            'date_of_entry_us' => 'date_of_entry_us',
            'status' => 'status'
        ];

        $clientData = [];

        foreach ($fieldMapping as $csvField => $clientField) {
            if (isset($row[$csvField]) && $row[$csvField] !== '') {
                $value = trim($row[$csvField]);
                
                // Transform specific fields
                switch ($clientField) {
                    case 'insurance_covered':
                        $clientData[$clientField] = in_array(strtolower($value), ['yes', 'true', '1', 'y']);
                        break;
                    case 'date_of_birth':
                    case 'date_of_entry_us':
                        $clientData[$clientField] = $this->parseDate($value);
                        break;
                    case 'marital_status':
                        $clientData[$clientField] = $this->normalizeMaritalStatus($value);
                        break;
                    case 'status':
                        $clientData[$clientField] = $this->normalizeStatus($value);
                        break;
                    default:
                        $clientData[$clientField] = $value;
                }
            }
        }

        return $clientData;
    }

    /**
     * Validate client data for import.
     *
     * @param array $clientData
     * @param int $rowNumber
     * @return array
     */
    private function validateClientData(array $clientData, int $rowNumber): array
    {
        $rules = Client::validationRules();
        
        // Modify rules for import (make email unique check conditional)
        $rules['email'] = 'required|email';
        
        $validator = Validator::make($clientData, $rules);
        
        $errors = [];
        $warnings = [];
        
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = "Row {$rowNumber}: {$error}";
            }
        }

        // Additional business logic validation
        if (!empty($clientData['ssn']) && !preg_match('/^\d{3}-\d{2}-\d{4}$/', $clientData['ssn'])) {
            $warnings[] = "Row {$rowNumber}: SSN format should be XXX-XX-XXXX";
        }

        if (!empty($clientData['phone']) && !preg_match('/^[\+]?[1-9][\d]{0,15}$/', $clientData['phone'])) {
            $warnings[] = "Row {$rowNumber}: Phone number format may be invalid";
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * Parse date from various formats.
     *
     * @param string $dateString
     * @return string|null
     */
    private function parseDate(string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        $formats = [
            'Y-m-d',
            'm/d/Y',
            'd/m/Y',
            'Y/m/d',
            'm-d-Y',
            'd-m-Y'
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date && $date->format($format) === $dateString) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    /**
     * Normalize marital status values.
     *
     * @param string $value
     * @return string
     */
    private function normalizeMaritalStatus(string $value): string
    {
        $normalized = strtolower(trim($value));
        
        $mapping = [
            'single' => 'single',
            's' => 'single',
            'unmarried' => 'single',
            'married' => 'married',
            'm' => 'married',
            'divorced' => 'divorced',
            'd' => 'divorced',
            'widowed' => 'widowed',
            'w' => 'widowed',
            'widow' => 'widowed'
        ];

        return $mapping[$normalized] ?? 'single';
    }

    /**
     * Normalize status values.
     *
     * @param string $value
     * @return string
     */
    private function normalizeStatus(string $value): string
    {
        $normalized = strtolower(trim($value));
        
        $mapping = [
            'active' => 'active',
            'a' => 'active',
            'inactive' => 'inactive',
            'i' => 'inactive',
            'archived' => 'archived',
            'arch' => 'archived'
        ];

        return $mapping[$normalized] ?? 'active';
    }

    /**
     * Get sample CSV headers for template download.
     *
     * @return array
     */
    public function getSampleHeaders(): array
    {
        return [
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'phone',
            'mobile_number',
            'work_number',
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
            'visa_status',
            'date_of_entry_us',
            'status'
        ];
    }

    /**
     * Generate CSV template for download.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function generateCsvTemplate()
    {
        $headers = $this->getSampleHeaders();
        
        // Sample data row
        $sampleData = [
            'John',
            'Michael',
            'Doe',
            'john.doe@example.com',
            '+1-555-123-4567',
            '+1-555-123-4567',
            '+1-555-987-6543',
            '123-45-6789',
            '1985-06-15',
            'married',
            'Software Engineer',
            'yes',
            '123 Main St',
            'Apt 4B',
            '12345',
            'New York',
            'NY',
            'USA',
            'H1B',
            '2020-01-15',
            'active'
        ];

        return response()->streamDownload(function () use ($headers, $sampleData) {
            $handle = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($handle, $headers);
            
            // Write sample data
            fputcsv($handle, $sampleData);
            
            fclose($handle);
        }, 'client_import_template_' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="client_import_template_' . date('Y-m-d') . '.csv"'
        ]);
    }

    /**
     * Get import statistics and validation summary.
     *
     * @param array $csvData
     * @return array
     */
    public function getImportStatistics(array $csvData): array
    {
        $stats = [
            'total_rows' => count($csvData),
            'valid_rows' => 0,
            'invalid_rows' => 0,
            'duplicate_emails' => 0,
            'missing_required_fields' => 0,
            'field_statistics' => []
        ];

        $emailsSeen = [];
        $fieldCounts = [];

        foreach ($csvData as $rowData) {
            $data = $rowData['data'];
            $clientData = $this->transformCsvRowToClientData($data);
            $validation = $this->validateClientData($clientData, $rowData['row_number']);

            if ($validation['is_valid']) {
                $stats['valid_rows']++;
            } else {
                $stats['invalid_rows']++;
            }

            // Check for duplicate emails within the CSV
            if (!empty($clientData['email'])) {
                if (isset($emailsSeen[$clientData['email']])) {
                    $stats['duplicate_emails']++;
                } else {
                    $emailsSeen[$clientData['email']] = true;
                }
            }

            // Count field usage
            foreach ($clientData as $field => $value) {
                if (!isset($fieldCounts[$field])) {
                    $fieldCounts[$field] = 0;
                }
                if (!empty($value)) {
                    $fieldCounts[$field]++;
                }
            }
        }

        $stats['field_statistics'] = $fieldCounts;

        return $stats;
    }
}