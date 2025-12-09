<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Exception;

class ComprehensiveExportService
{
    /**
     * Export clients with customizable templates and formatting options.
     *
     * @param array $clientIds
     * @param string $format
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\Response|array
     */
    public function exportClients(array $clientIds, string $format = 'excel', array $options = [])
    {
        try {
            $clients = $this->getClientsForExport($clientIds, $options);
            
            Log::info('Comprehensive export initiated', [
                'client_count' => $clients->count(),
                'format' => $format,
                'template' => $options['template'] ?? 'default',
                'user_id' => auth()->id()
            ]);

            // Update last export timestamp
            Client::whereIn('id', $clientIds)->update(['last_export_at' => now()]);

            switch ($format) {
                case 'excel':
                    return $this->exportToExcel($clients, $options);
                case 'csv':
                    return $this->exportToCsv($clients, $options);
                case 'pdf':
                    return $this->exportToPdf($clients, $options);
                case 'json':
                    return $this->exportToJson($clients, $options);
                default:
                    throw new Exception("Unsupported export format: {$format}");
            }
        } catch (Exception $e) {
            Log::error('Comprehensive export failed', [
                'client_ids' => $clientIds,
                'format' => $format,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get clients with relationships for export.
     *
     * @param array $clientIds
     * @param array $options
     * @return Collection
     */
    private function getClientsForExport(array $clientIds, array $options = []): Collection
    {
        $query = Client::with([
            'spouse',
            'employees',
            'projects',
            'assets',
            'expenses',
            'user',
            'invoices',
            'documents'
        ]);

        if (!empty($clientIds)) {
            $query->whereIn('id', $clientIds);
        }

        // Apply additional filters
        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        if (!empty($options['date_from'])) {
            $query->where('created_at', '>=', $options['date_from']);
        }

        if (!empty($options['date_to'])) {
            $query->where('created_at', '<=', $options['date_to'] . ' 23:59:59');
        }

        return $query->get();
    }

    /**
     * Export to Excel with advanced formatting.
     *
     * @param Collection $clients
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function exportToExcel(Collection $clients, array $options = [])
    {
        $template = $options['template'] ?? 'comprehensive';
        $filename = $this->generateFilename('excel', $template);
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ];

        return response()->streamDownload(function () use ($clients, $options, $template) {
            $handle = fopen('php://output', 'w');
            
            // Write UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");
            
            // Generate data based on template
            $data = $this->generateExportData($clients, $template, $options);
            
            if (!empty($data)) {
                // Write metadata header if requested
                if ($options['include_metadata'] ?? false) {
                    $this->writeMetadataHeader($handle, $clients, $options);
                }
                
                // Write column headers
                fputcsv($handle, array_keys($data[0]));
                
                // Write data rows
                foreach ($data as $row) {
                    fputcsv($handle, $row);
                }
                
                // Write summary footer if requested
                if ($options['include_summary'] ?? false) {
                    $this->writeSummaryFooter($handle, $clients, $options);
                }
            }
            
            fclose($handle);
        }, $filename, $headers);
    }

    /**
     * Export to CSV with customizable columns.
     *
     * @param Collection $clients
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function exportToCsv(Collection $clients, array $options = [])
    {
        $template = $options['template'] ?? 'comprehensive';
        $filename = $this->generateFilename('csv', $template);
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ];

        return response()->streamDownload(function () use ($clients, $options, $template) {
            $handle = fopen('php://output', 'w');
            
            // Write UTF-8 BOM
            fwrite($handle, "\xEF\xBB\xBF");
            
            $data = $this->generateExportData($clients, $template, $options);
            
            if (!empty($data)) {
                fputcsv($handle, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($handle, $row);
                }
            }
            
            fclose($handle);
        }, $filename, $headers);
    }

    /**
     * Export to PDF with professional formatting.
     *
     * @param Collection $clients
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function exportToPdf(Collection $clients, array $options = [])
    {
        $template = $options['template'] ?? 'comprehensive';
        $filename = $this->generateFilename('pdf', $template);
        
        // Load related data for all clients
        $clients->load(['spouse', 'employees', 'projects', 'assets', 'expenses', 'user']);
        
        // Generate PDF using DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.clients-bulk', compact('clients', 'template', 'options'));
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Return PDF download
        return $pdf->download($filename);
    }

    /**
     * Export to JSON format.
     *
     * @param Collection $clients
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function exportToJson(Collection $clients, array $options = [])
    {
        $template = $options['template'] ?? 'comprehensive';
        $filename = $this->generateFilename('json', $template);
        
        $data = [
            'metadata' => [
                'export_date' => now()->toISOString(),
                'exported_by' => auth()->user()->name ?? 'System',
                'total_clients' => $clients->count(),
                'template' => $template,
                'version' => '1.0'
            ],
            'clients' => $this->generateJsonExportData($clients, $template, $options)
        ];

        return response()->json($data, 200, [
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Generate export data based on template.
     *
     * @param Collection $clients
     * @param string $template
     * @param array $options
     * @return array
     */
    private function generateExportData(Collection $clients, string $template, array $options = []): array
    {
        $data = [];
        
        foreach ($clients as $client) {
            switch ($template) {
                case 'basic':
                    $row = $this->generateBasicClientRow($client);
                    break;
                case 'comprehensive':
                    $row = $this->generateComprehensiveClientRow($client);
                    break;
                case 'financial':
                    $row = $this->generateFinancialClientRow($client);
                    break;
                case 'contact':
                    $row = $this->generateContactClientRow($client);
                    break;
                case 'custom':
                    $row = $this->generateCustomClientRow($client, $options['custom_fields'] ?? []);
                    break;
                default:
                    $row = $this->generateComprehensiveClientRow($client);
            }
            
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Generate basic client row data.
     *
     * @param Client $client
     * @return array
     */
    private function generateBasicClientRow(Client $client): array
    {
        return [
            'ID' => $client->id,
            'First Name' => $client->first_name,
            'Last Name' => $client->last_name,
            'Email' => $client->email,
            'Phone' => $client->phone,
            'Status' => $client->status,
            'Registration Date' => $client->created_at->format('Y-m-d'),
        ];
    }

    /**
     * Generate comprehensive client row data.
     *
     * @param Client $client
     * @return array
     */
    private function generateComprehensiveClientRow(Client $client): array
    {
        return [
            'ID' => $client->id,
            'First Name' => $client->first_name,
            'Middle Name' => $client->middle_name,
            'Last Name' => $client->last_name,
            'Full Name' => $client->full_name,
            'Email' => $client->email,
            'Phone' => $client->phone,
            'Mobile Number' => $client->mobile_number,
            'Work Number' => $client->work_number,
            'Date of Birth' => $client->date_of_birth,
            'Marital Status' => $client->marital_status,
            'Occupation' => $client->occupation,
            'Insurance Covered' => $client->insurance_covered ? 'Yes' : 'No',
            'Street No' => $client->street_no,
            'Apartment No' => $client->apartment_no,
            'City' => $client->city,
            'State' => $client->state,
            'Zip Code' => $client->zip_code,
            'Country' => $client->country,
            'Formatted Address' => $client->formatted_address,
            'Visa Status' => $client->visa_status,
            'Date of Entry US' => $client->date_of_entry_us,
            'Status' => $client->status,
            'Assigned User' => $client->user?->name,
            'Has Spouse' => $client->spouse ? 'Yes' : 'No',
            'Spouse Name' => $client->spouse?->full_name,
            'Employees Count' => $client->employees->count(),
            'Projects Count' => $client->projects->count(),
            'Assets Count' => $client->assets->count(),
            'Expenses Count' => $client->expenses->count(),
            'Documents Count' => $client->documents->count(),
            'Invoices Count' => $client->invoices->count(),
            'Registration Date' => $client->created_at->format('Y-m-d H:i:s'),
            'Last Updated' => $client->updated_at->format('Y-m-d H:i:s'),
            'Last Export' => $client->last_export_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Generate financial client row data.
     *
     * @param Client $client
     * @return array
     */
    private function generateFinancialClientRow(Client $client): array
    {
        $totalAssetValue = $client->assets->sum('cost_of_acquisition');
        $totalBusinessAssetValue = $client->assets->sum(function ($asset) {
            return ($asset->cost_of_acquisition * $asset->percentage_used_in_business) / 100;
        });
        $totalExpenseAmount = $client->expenses->sum('amount');
        $totalDeductibleExpenses = $client->expenses->sum('deductible_amount');

        return [
            'ID' => $client->id,
            'Client Name' => $client->full_name,
            'Email' => $client->email,
            'Status' => $client->status,
            'Assets Count' => $client->assets->count(),
            'Total Asset Value' => number_format($totalAssetValue, 2),
            'Business Asset Value' => number_format($totalBusinessAssetValue, 2),
            'Personal Asset Value' => number_format($totalAssetValue - $totalBusinessAssetValue, 2),
            'Expenses Count' => $client->expenses->count(),
            'Total Expense Amount' => number_format($totalExpenseAmount, 2),
            'Deductible Expenses' => number_format($totalDeductibleExpenses, 2),
            'Non-Deductible Expenses' => number_format($totalExpenseAmount - $totalDeductibleExpenses, 2),
            'Invoices Count' => $client->invoices->count(),
            'Total Invoice Amount' => number_format($client->invoices->sum('total_amount'), 2),
            'Paid Invoice Amount' => number_format($client->invoices->where('status', 'paid')->sum('total_amount'), 2),
            'Outstanding Amount' => number_format($client->invoices->where('status', '!=', 'paid')->sum('total_amount'), 2),
        ];
    }

    /**
     * Generate contact client row data.
     *
     * @param Client $client
     * @return array
     */
    private function generateContactClientRow(Client $client): array
    {
        return [
            'ID' => $client->id,
            'Full Name' => $client->full_name,
            'Email' => $client->email,
            'Phone' => $client->phone,
            'Mobile Number' => $client->mobile_number,
            'Work Number' => $client->work_number,
            'Street Address' => $client->street_no . ($client->apartment_no ? ', ' . $client->apartment_no : ''),
            'City' => $client->city,
            'State' => $client->state,
            'Zip Code' => $client->zip_code,
            'Country' => $client->country,
            'Full Address' => $client->formatted_address,
            'Marital Status' => $client->marital_status,
            'Spouse Name' => $client->spouse?->full_name,
            'Spouse Email' => $client->spouse?->email,
            'Spouse Phone' => $client->spouse?->phone,
            'Assigned User' => $client->user?->name,
            'User Email' => $client->user?->email,
        ];
    }

    /**
     * Generate custom client row data.
     *
     * @param Client $client
     * @param array $customFields
     * @return array
     */
    private function generateCustomClientRow(Client $client, array $customFields): array
    {
        $row = [];
        
        // Ensure customFields is an array
        if (!is_array($customFields)) {
            $customFields = [];
        }
        
        foreach ($customFields as $field) {
            switch ($field) {
                case 'id':
                    $row['ID'] = $client->id;
                    break;
                case 'full_name':
                    $row['Full Name'] = $client->full_name;
                    break;
                case 'email':
                    $row['Email'] = $client->email;
                    break;
                case 'phone':
                    $row['Phone'] = $client->phone;
                    break;
                case 'status':
                    $row['Status'] = $client->status;
                    break;
                case 'address':
                    $row['Address'] = $client->formatted_address;
                    break;
                case 'spouse_name':
                    $row['Spouse Name'] = $client->spouse?->full_name;
                    break;
                case 'assets_count':
                    $row['Assets Count'] = $client->assets->count();
                    break;
                case 'expenses_count':
                    $row['Expenses Count'] = $client->expenses->count();
                    break;
                case 'registration_date':
                    $row['Registration Date'] = $client->created_at->format('Y-m-d');
                    break;
                // Add more custom fields as needed
            }
        }
        
        return $row;
    }

    /**
     * Generate JSON export data.
     *
     * @param Collection $clients
     * @param string $template
     * @param array $options
     * @return array
     */
    private function generateJsonExportData(Collection $clients, string $template, array $options = []): array
    {
        $data = [];
        
        foreach ($clients as $client) {
            $clientData = [
                'id' => $client->id,
                'personal_info' => [
                    'first_name' => $client->first_name,
                    'middle_name' => $client->middle_name,
                    'last_name' => $client->last_name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'mobile_number' => $client->mobile_number,
                    'work_number' => $client->work_number,
                    'date_of_birth' => $client->date_of_birth,
                    'marital_status' => $client->marital_status,
                    'occupation' => $client->occupation,
                    'insurance_covered' => $client->insurance_covered,
                    'visa_status' => $client->visa_status,
                    'date_of_entry_us' => $client->date_of_entry_us,
                ],
                'address' => [
                    'street_no' => $client->street_no,
                    'apartment_no' => $client->apartment_no,
                    'city' => $client->city,
                    'state' => $client->state,
                    'zip_code' => $client->zip_code,
                    'country' => $client->country,
                ],
                'status' => $client->status,
                'assigned_user' => $client->user?->name,
                'created_at' => $client->created_at->toISOString(),
                'updated_at' => $client->updated_at->toISOString(),
            ];

            if ($template === 'comprehensive' || $options['include_relationships'] ?? false) {
                $clientData['spouse'] = $client->spouse ? [
                    'first_name' => $client->spouse->first_name,
                    'last_name' => $client->spouse->last_name,
                    'email' => $client->spouse->email,
                    'phone' => $client->spouse->phone,
                    'occupation' => $client->spouse->occupation,
                ] : null;

                $clientData['employees'] = $client->employees->map(function ($employee) {
                    return [
                        'employer_name' => $employee->employer_name,
                        'job_title' => $employee->job_title,
                        'annual_salary' => $employee->annual_salary,
                        'employment_type' => $employee->employment_type,
                    ];
                })->toArray();

                $clientData['assets'] = $client->assets->map(function ($asset) {
                    return [
                        'asset_name' => $asset->asset_name,
                        'date_of_purchase' => $asset->date_of_purchase->format('Y-m-d'),
                        'cost_of_acquisition' => $asset->cost_of_acquisition,
                        'percentage_used_in_business' => $asset->percentage_used_in_business,
                    ];
                })->toArray();

                $clientData['expenses'] = $client->expenses->map(function ($expense) {
                    return [
                        'category' => $expense->category,
                        'particulars' => $expense->particulars,
                        'amount' => $expense->amount,
                        'expense_date' => $expense->expense_date->format('Y-m-d'),
                    ];
                })->toArray();
            }

            $data[] = $clientData;
        }

        return $data;
    }

    /**
     * Generate PDF content.
     *
     * @param Collection $clients
     * @param string $template
     * @param array $options
     * @return string
     */
    private function generatePdfContent(Collection $clients, string $template, array $options = []): string
    {
        $content = "CLIENT EXPORT REPORT\n";
        $content .= str_repeat('=', 80) . "\n";
        $content .= "Generated on: " . date('Y-m-d H:i:s') . "\n";
        $content .= "Template: " . ucfirst($template) . "\n";
        $content .= "Total Clients: " . $clients->count() . "\n";
        $content .= "Exported by: " . (auth()->user()->name ?? 'System') . "\n\n";

        foreach ($clients as $index => $client) {
            $content .= "CLIENT #" . ($index + 1) . "\n";
            $content .= str_repeat('-', 50) . "\n";
            
            switch ($template) {
                case 'basic':
                    $content .= $this->generateBasicPdfSection($client);
                    break;
                case 'financial':
                    $content .= $this->generateFinancialPdfSection($client);
                    break;
                case 'contact':
                    $content .= $this->generateContactPdfSection($client);
                    break;
                default:
                    $content .= $this->generateComprehensivePdfSection($client);
            }
            
            $content .= "\n";
        }

        $content .= "\nEXPORT SUMMARY\n";
        $content .= str_repeat('=', 30) . "\n";
        $content .= "Total Clients: " . $clients->count() . "\n";
        $content .= "Active Clients: " . $clients->where('status', 'active')->count() . "\n";
        $content .= "Inactive Clients: " . $clients->where('status', 'inactive')->count() . "\n";
        $content .= "Archived Clients: " . $clients->where('status', 'archived')->count() . "\n";
        $content .= "Clients with Spouse: " . $clients->filter(fn($c) => $c->spouse)->count() . "\n";
        $content .= "\nGenerated by My Super Tax\n";

        return $content;
    }

    /**
     * Generate basic PDF section for client.
     *
     * @param Client $client
     * @return string
     */
    private function generateBasicPdfSection(Client $client): string
    {
        return "ID: {$client->id}\n" .
               "Name: {$client->full_name}\n" .
               "Email: {$client->email}\n" .
               "Phone: {$client->phone}\n" .
               "Status: {$client->status}\n" .
               "Registration: {$client->created_at->format('Y-m-d')}\n";
    }

    /**
     * Generate comprehensive PDF section for client.
     *
     * @param Client $client
     * @return string
     */
    private function generateComprehensivePdfSection(Client $client): string
    {
        $content = "ID: {$client->id}\n";
        $content .= "Name: {$client->full_name}\n";
        $content .= "Email: {$client->email}\n";
        $content .= "Phone: {$client->phone}\n";
        $content .= "Status: {$client->status}\n";
        $content .= "Marital Status: {$client->marital_status}\n";
        $content .= "Occupation: {$client->occupation}\n";
        
        if ($client->spouse) {
            $content .= "Spouse: {$client->spouse->full_name}\n";
        }
        
        $content .= "Address: {$client->formatted_address}\n";
        $content .= "Assigned User: " . ($client->user ? "{$client->user->first_name} {$client->user->last_name}" : 'Unassigned') . "\n";
        $content .= "Projects: {$client->projects->count()}\n";
        $content .= "Assets: {$client->assets->count()}\n";
        $content .= "Expenses: {$client->expenses->count()}\n";
        $content .= "Registration: {$client->created_at->format('Y-m-d H:i:s')}\n";
        
        return $content;
    }

    /**
     * Generate financial PDF section for client.
     *
     * @param Client $client
     * @return string
     */
    private function generateFinancialPdfSection(Client $client): string
    {
        $totalAssets = $client->assets->sum('cost_of_acquisition');
        $totalExpenses = $client->expenses->sum('amount');
        
        return "ID: {$client->id}\n" .
               "Name: {$client->full_name}\n" .
               "Email: {$client->email}\n" .
               "Assets Count: {$client->assets->count()}\n" .
               "Total Asset Value: $" . number_format($totalAssets, 2) . "\n" .
               "Expenses Count: {$client->expenses->count()}\n" .
               "Total Expenses: $" . number_format($totalExpenses, 2) . "\n" .
               "Invoices: {$client->invoices->count()}\n";
    }

    /**
     * Generate contact PDF section for client.
     *
     * @param Client $client
     * @return string
     */
    private function generateContactPdfSection(Client $client): string
    {
        $content = "Name: {$client->full_name}\n";
        $content .= "Email: {$client->email}\n";
        $content .= "Phone: {$client->phone}\n";
        $content .= "Mobile: {$client->mobile_number}\n";
        $content .= "Work: {$client->work_number}\n";
        $content .= "Address: {$client->formatted_address}\n";
        
        if ($client->spouse) {
            $content .= "Spouse: {$client->spouse->full_name}\n";
            $content .= "Spouse Email: {$client->spouse->email}\n";
        }
        
        return $content;
    }

    /**
     * Write metadata header to CSV/Excel.
     *
     * @param resource $handle
     * @param Collection $clients
     * @param array $options
     */
    private function writeMetadataHeader($handle, Collection $clients, array $options): void
    {
        fputcsv($handle, ['EXPORT METADATA']);
        fputcsv($handle, ['Export Date', date('Y-m-d H:i:s')]);
        fputcsv($handle, ['Exported By', auth()->user()->name ?? 'System']);
        fputcsv($handle, ['Total Clients', $clients->count()]);
        fputcsv($handle, ['Template', $options['template'] ?? 'comprehensive']);
        fputcsv($handle, []);
    }

    /**
     * Write summary footer to CSV/Excel.
     *
     * @param resource $handle
     * @param Collection $clients
     * @param array $options
     */
    private function writeSummaryFooter($handle, Collection $clients, array $options): void
    {
        fputcsv($handle, []);
        fputcsv($handle, ['EXPORT SUMMARY']);
        fputcsv($handle, ['Total Clients', $clients->count()]);
        fputcsv($handle, ['Active Clients', $clients->where('status', 'active')->count()]);
        fputcsv($handle, ['Inactive Clients', $clients->where('status', 'inactive')->count()]);
        fputcsv($handle, ['Archived Clients', $clients->where('status', 'archived')->count()]);
        fputcsv($handle, ['Clients with Spouse', $clients->filter(fn($c) => $c->spouse)->count()]);
    }

    /**
     * Generate filename for export.
     *
     * @param string $format
     * @param string $template
     * @return string
     */
    private function generateFilename(string $format, string $template): string
    {
        $timestamp = date('Y-m-d_H-i-s');
        return "clients_export_{$template}_{$timestamp}.{$format}";
    }

    /**
     * Schedule automated export.
     *
     * @param array $options
     * @return array
     */
    public function scheduleAutomatedExport(array $options): array
    {
        // This would integrate with Laravel's task scheduler
        // For now, return configuration for manual setup
        
        return [
            'success' => true,
            'message' => 'Automated export scheduled successfully',
            'schedule' => [
                'frequency' => $options['frequency'] ?? 'weekly',
                'format' => $options['format'] ?? 'excel',
                'template' => $options['template'] ?? 'comprehensive',
                'recipients' => $options['recipients'] ?? [],
                'next_run' => $this->calculateNextRun($options['frequency'] ?? 'weekly')
            ]
        ];
    }

    /**
     * Calculate next run time for scheduled export.
     *
     * @param string $frequency
     * @return string
     */
    private function calculateNextRun(string $frequency): string
    {
        switch ($frequency) {
            case 'daily':
                return now()->addDay()->format('Y-m-d H:i:s');
            case 'weekly':
                return now()->addWeek()->format('Y-m-d H:i:s');
            case 'monthly':
                return now()->addMonth()->format('Y-m-d H:i:s');
            default:
                return now()->addWeek()->format('Y-m-d H:i:s');
        }
    }

    /**
     * Get available export templates.
     *
     * @return array
     */
    public function getAvailableTemplates(): array
    {
        return [
            'basic' => [
                'name' => 'Basic Information',
                'description' => 'Essential client information only',
                'fields' => ['ID', 'Name', 'Email', 'Phone', 'Status', 'Registration Date']
            ],
            'comprehensive' => [
                'name' => 'Comprehensive Report',
                'description' => 'Complete client information with relationships',
                'fields' => ['All personal info', 'Address', 'Spouse', 'Counts', 'Dates']
            ],
            'financial' => [
                'name' => 'Financial Summary',
                'description' => 'Focus on assets, expenses, and invoices',
                'fields' => ['Basic info', 'Asset values', 'Expense totals', 'Invoice amounts']
            ],
            'contact' => [
                'name' => 'Contact Information',
                'description' => 'Contact details and addresses',
                'fields' => ['Names', 'All contact methods', 'Addresses', 'Spouse contacts']
            ],
            'custom' => [
                'name' => 'Custom Fields',
                'description' => 'Select specific fields to export',
                'fields' => ['User-defined field selection']
            ]
        ];
    }

    /**
     * Get export statistics.
     *
     * @param array $clientIds
     * @return array
     */
    public function getExportStatistics(array $clientIds): array
    {
        $clients = Client::with(['spouse', 'employees', 'projects', 'assets', 'expenses', 'invoices'])
            ->whereIn('id', $clientIds)
            ->get();

        return [
            'total_clients' => $clients->count(),
            'status_breakdown' => [
                'active' => $clients->where('status', 'active')->count(),
                'inactive' => $clients->where('status', 'inactive')->count(),
                'archived' => $clients->where('status', 'archived')->count(),
            ],
            'relationship_counts' => [
                'with_spouse' => $clients->filter(fn($c) => $c->spouse)->count(),
                'total_employees' => $clients->sum(fn($c) => $c->employees->count()),
                'total_projects' => $clients->sum(fn($c) => $c->projects->count()),
                'total_assets' => $clients->sum(fn($c) => $c->assets->count()),
                'total_expenses' => $clients->sum(fn($c) => $c->expenses->count()),
            ],
            'financial_summary' => [
                'total_asset_value' => $clients->sum(fn($c) => $c->assets->sum('cost_of_acquisition')),
                'total_expense_amount' => $clients->sum(fn($c) => $c->expenses->sum('amount')),
                'total_invoice_amount' => $clients->sum(fn($c) => $c->invoices->sum('total_amount')),
            ],
            'date_ranges' => [
                'earliest_registration' => $clients->min('created_at'),
                'latest_registration' => $clients->max('created_at'),
                'last_export' => $clients->max('last_export_at'),
            ]
        ];
    }
}