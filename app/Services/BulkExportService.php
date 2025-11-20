<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class BulkExportService
{
    /**
     * Export selected clients data.
     *
     * @param array $clientIds
     * @param string $format
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportSelectedClients(array $clientIds, string $format = 'excel', array $options = [])
    {
        try {
            $clients = Client::with(['spouse', 'employees', 'projects', 'assets', 'expenses'])
                ->whereIn('id', $clientIds)
                ->get();

            Log::info('Bulk export initiated', [
                'client_count' => $clients->count(),
                'format' => $format,
                'user_id' => auth()->id()
            ]);

            switch ($format) {
                case 'excel':
                    return $this->exportToExcel($clients, $options);
                case 'csv':
                    return $this->exportToCsv($clients, $options);
                case 'pdf':
                    return $this->exportToPdf($clients, $options);
                default:
                    throw new Exception("Unsupported export format: {$format}");
            }
        } catch (Exception $e) {
            Log::error('Bulk export failed', [
                'client_ids' => $clientIds,
                'format' => $format,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Export clients to Excel format.
     *
     * @param Collection $clients
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function exportToExcel(Collection $clients, array $options = [])
    {
        $filename = 'clients_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ];

        $data = $this->prepareExportData($clients, $options);
        
        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            
            // Write UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");
            
            // Write headers
            if (!empty($data)) {
                fputcsv($handle, array_keys($data[0]));
                
                // Write data
                foreach ($data as $row) {
                    fputcsv($handle, $row);
                }
            }
            
            fclose($handle);
        }, $filename, $headers);
    }

    /**
     * Export clients to CSV format.
     *
     * @param Collection $clients
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function exportToCsv(Collection $clients, array $options = [])
    {
        $filename = 'clients_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ];

        $data = $this->prepareExportData($clients, $options);

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            
            // Write UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");
            
            // Write headers
            if (!empty($data)) {
                fputcsv($handle, array_keys($data[0]));
                
                // Write data
                foreach ($data as $row) {
                    fputcsv($handle, $row);
                }
            }
            
            fclose($handle);
        }, $filename, $headers);
    }

    /**
     * Export clients to PDF format.
     *
     * @param Collection $clients
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function exportToPdf(Collection $clients, array $options = [])
    {
        $filename = 'clients_export_' . date('Y-m-d_H-i-s') . '.pdf';
        $template = $options['template'] ?? 'basic';
        
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
     * Prepare data for export.
     *
     * @param Collection $clients
     * @param array $options
     * @return array
     */
    private function prepareExportData(Collection $clients, array $options = []): array
    {
        $includeRelationships = $options['include_relationships'] ?? false;
        $data = [];

        foreach ($clients as $client) {
            $row = [
                'ID' => $client->id,
                'First Name' => $client->first_name,
                'Middle Name' => $client->middle_name,
                'Last Name' => $client->last_name,
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
                'Visa Status' => $client->visa_status,
                'Date of Entry US' => $client->date_of_entry_us,
                'Status' => $client->status,
                'Registration Date' => $client->created_at->format('Y-m-d H:i:s'),
                'Last Updated' => $client->updated_at->format('Y-m-d H:i:s'),
            ];

            if ($includeRelationships) {
                // Add relationship counts
                $row['Has Spouse'] = $client->spouse ? 'Yes' : 'No';
                $row['Employees Count'] = $client->employees->count();
                $row['Projects Count'] = $client->projects->count();
                $row['Assets Count'] = $client->assets->count();
                $row['Expenses Count'] = $client->expenses->count();

                // Add spouse information if available
                if ($client->spouse) {
                    $row['Spouse First Name'] = $client->spouse->first_name;
                    $row['Spouse Last Name'] = $client->spouse->last_name;
                    $row['Spouse Email'] = $client->spouse->email;
                    $row['Spouse Phone'] = $client->spouse->phone;
                    $row['Spouse Occupation'] = $client->spouse->occupation;
                }

                // Add financial summary
                $row['Total Asset Value'] = $client->assets->sum('cost_of_acquisition');
                $row['Total Business Asset Value'] = $client->assets->sum(function ($asset) {
                    return ($asset->cost_of_acquisition * $asset->percentage_used_in_business) / 100;
                });
                $row['Total Expense Amount'] = $client->expenses->sum('amount');
                $row['Total Deductible Expenses'] = $client->expenses->sum('deductible_amount');
            }

            $data[] = $row;
        }

        return $data;
    }

    /**
     * Generate PDF content.
     *
     * @param Collection $clients
     * @param array $options
     * @return string
     */
    private function generatePdfContent(Collection $clients, array $options = []): string
    {
        $content = "CLIENT EXPORT REPORT\n";
        $content .= str_repeat('=', 50) . "\n";
        $content .= "Generated on: " . date('Y-m-d H:i:s') . "\n";
        $content .= "Total Clients: " . $clients->count() . "\n";
        $content .= "Exported by: " . (auth()->user()->name ?? 'System') . "\n\n";

        foreach ($clients as $index => $client) {
            $content .= "CLIENT #" . ($index + 1) . "\n";
            $content .= str_repeat('-', 30) . "\n";
            $content .= "ID: {$client->id}\n";
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
            $content .= "Registration Date: {$client->created_at->format('Y-m-d')}\n";
            
            // Add relationship counts
            $content .= "Projects: {$client->projects->count()}\n";
            $content .= "Assets: {$client->assets->count()}\n";
            $content .= "Expenses: {$client->expenses->count()}\n";
            
            $content .= "\n";
        }

        $content .= "\nEND OF REPORT\n";
        $content .= "Generated by My Super Tax\n";

        return $content;
    }

    /**
     * Get export statistics.
     *
     * @param array $clientIds
     * @return array
     */
    public function getExportStatistics(array $clientIds): array
    {
        $clients = Client::with(['spouse', 'employees', 'projects', 'assets', 'expenses'])
            ->whereIn('id', $clientIds)
            ->get();

        return [
            'total_clients' => $clients->count(),
            'clients_with_spouse' => $clients->filter(fn($c) => $c->spouse)->count(),
            'total_employees' => $clients->sum(fn($c) => $c->employees->count()),
            'total_projects' => $clients->sum(fn($c) => $c->projects->count()),
            'total_assets' => $clients->sum(fn($c) => $c->assets->count()),
            'total_expenses' => $clients->sum(fn($c) => $c->expenses->count()),
            'status_breakdown' => $clients->groupBy('status')->map->count(),
            'marital_status_breakdown' => $clients->groupBy('marital_status')->map->count(),
            'total_asset_value' => $clients->sum(fn($c) => $c->assets->sum('cost_of_acquisition')),
            'total_expense_amount' => $clients->sum(fn($c) => $c->expenses->sum('amount')),
        ];
    }

    /**
     * Generate export preview.
     *
     * @param array $clientIds
     * @param int $limit
     * @return array
     */
    public function generateExportPreview(array $clientIds, int $limit = 5): array
    {
        $clients = Client::with(['spouse', 'employees', 'projects', 'assets', 'expenses'])
            ->whereIn('id', $clientIds)
            ->limit($limit)
            ->get();

        $preview = $this->prepareExportData($clients);
        
        return [
            'preview_data' => $preview,
            'total_clients' => count($clientIds),
            'preview_count' => $clients->count(),
            'columns' => !empty($preview) ? array_keys($preview[0]) : [],
        ];
    }
}