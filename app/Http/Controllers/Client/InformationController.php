<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientInformationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class InformationController extends Controller
{
    public function __construct(
        private ClientInformationService $clientInformationService
    ) {}

    /**
     * Display the client information form
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ensure user has client role
        if (!$user->hasRole('client')) {
            abort(403, 'Access denied. Client role required.');
        }
        
        // Find or create client record for this user with proper authorization
        $client = $this->findOrCreateClientRecord($user);
        
        // Check if user can access this client information
        Gate::authorize('view', $client);

        // Load all related data with optimized queries
        $client->load([
            'spouse', 
            'employees', 
            'projects', 
            'assets', 
            'expenses'
        ]);

        // Prepare client data for the form with enhanced structure
        $clientData = $this->prepareClientDataForForm($client);
        
        // Get completion status and progress tracking
        $completionStatus = $this->calculateCompletionStatus($client);
        
        // Get last sync timestamp for real-time updates
        $lastSyncTimestamp = Cache::get("client_sync_{$client->id}", now());

        return Inertia::render('Client/Information', [
            'clientData' => $clientData,
            'client' => $client->only(['id', 'status', 'created_at', 'updated_at']),
            'completionStatus' => $completionStatus,
            'lastSyncTimestamp' => $lastSyncTimestamp,
            'permissions' => [
                'canEdit' => Gate::allows('update', $client),
                'canExport' => Gate::allows('export', $client),
                'canRequestReview' => Gate::allows('requestReview', $client),
            ]
        ]);
    }

    /**
     * Find or create client record with proper validation
     */
    private function findOrCreateClientRecord(User $user): Client
    {
        $client = Client::where('email', $user->email)->first();
        
        if (!$client) {
            // Create a basic client record with minimal required fields
            $client = DB::transaction(function () use ($user) {
                return Client::create([
                    'user_id' => $user->id,
                    'first_name' => $user->name ? explode(' ', $user->name)[0] : '',
                    'last_name' => $user->name ? (explode(' ', $user->name)[1] ?? '') : '',
                    'email' => $user->email,
                    'status' => 'active'
                ]);
            });
            
            Log::info('Created new client record', [
                'client_id' => $client->id,
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }
        
        return $client;
    }

    /**
     * Prepare client data for form with enhanced structure
     */
    private function prepareClientDataForForm(Client $client): array
    {
        return [
            'personal' => [
                'first_name' => $client->first_name ?? '',
                'middle_name' => $client->middle_name ?? '',
                'last_name' => $client->last_name ?? '',
                'email' => $client->email ?? '',
                'phone' => $client->phone ?? '',
                'ssn' => $client->ssn ?? '',
                'date_of_birth' => $client->date_of_birth?->format('Y-m-d') ?? '',
                'marital_status' => $client->marital_status ?? 'single',
                'occupation' => $client->occupation ?? '',
                'insurance_covered' => $client->insurance_covered ?? false,
                'street_no' => $client->street_no ?? '',
                'apartment_no' => $client->apartment_no ?? '',
                'zip_code' => $client->zip_code ?? '',
                'city' => $client->city ?? '',
                'state' => $client->state ?? '',
                'country' => $client->country ?? 'United States',
                'mobile_number' => $client->mobile_number ?? '',
                'work_number' => $client->work_number ?? '',
                'visa_status' => $client->visa_status ?? '',
                'date_of_entry_us' => $client->date_of_entry_us?->format('Y-m-d') ?? '',
            ],
            'spouse' => $client->spouse ? [
                'first_name' => $client->spouse->first_name,
                'middle_name' => $client->spouse->middle_name,
                'last_name' => $client->spouse->last_name,
                'email' => $client->spouse->email,
                'phone' => $client->spouse->phone,
                'ssn' => $client->spouse->ssn,
                'date_of_birth' => $client->spouse->date_of_birth?->format('Y-m-d'),
                'occupation' => $client->spouse->occupation,
            ] : [],
            'employee' => $client->employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'employer_name' => $employee->employer_name,
                    'position' => $employee->position,
                    'start_date' => $employee->start_date?->format('Y-m-d'),
                    'end_date' => $employee->end_date?->format('Y-m-d'),
                    'employment_status' => $employee->employment_status,
                    'salary' => $employee->salary,
                    'pay_frequency' => $employee->pay_frequency,
                    'work_location' => $employee->work_location,
                    'notes' => $employee->notes,
                    'benefits' => $employee->benefits ?? [],
                ];
            })->toArray(),
            'projects' => $client->projects->toArray(),
            'assets' => $client->assets->toArray(),
            'expenses' => $client->expenses->toArray(),
        ];
    }

    /**
     * Calculate completion status for progress tracking
     */
    private function calculateCompletionStatus(Client $client): array
    {
        $sections = [
            'personal' => $this->calculatePersonalCompletion($client),
            'spouse' => $this->calculateSpouseCompletion($client),
            'employee' => $this->calculateEmployeeCompletion($client),
            'projects' => $this->calculateProjectsCompletion($client),
            'assets' => $this->calculateAssetsCompletion($client),
            'expenses' => $this->calculateExpensesCompletion($client),
        ];
        
        $overallCompletion = array_sum($sections) / count($sections);
        
        return [
            'sections' => $sections,
            'overall' => round($overallCompletion, 2),
            'lastUpdated' => $client->updated_at->toISOString(),
        ];
    }

    /**
     * Calculate personal section completion percentage
     */
    private function calculatePersonalCompletion(Client $client): float
    {
        $requiredFields = [
            'first_name', 'last_name', 'email', 'phone', 'ssn', 
            'date_of_birth', 'marital_status', 'street_no', 
            'zip_code', 'city', 'state'
        ];
        
        $filledFields = 0;
        foreach ($requiredFields as $field) {
            if (!empty($client->$field)) {
                $filledFields++;
            }
        }
        
        return ($filledFields / count($requiredFields)) * 100;
    }

    /**
     * Calculate spouse section completion percentage
     */
    private function calculateSpouseCompletion(Client $client): float
    {
        if ($client->marital_status !== 'married') {
            return 100; // Not applicable
        }
        
        if (!$client->spouse) {
            return 0;
        }
        
        $requiredFields = ['first_name', 'last_name'];
        $filledFields = 0;
        
        foreach ($requiredFields as $field) {
            if (!empty($client->spouse->$field)) {
                $filledFields++;
            }
        }
        
        return ($filledFields / count($requiredFields)) * 100;
    }

    /**
     * Calculate employee section completion percentage
     */
    private function calculateEmployeeCompletion(Client $client): float
    {
        if ($client->employees->isEmpty()) {
            return 0;
        }
        
        $totalEmployees = $client->employees->count();
        $completeEmployees = 0;
        
        foreach ($client->employees as $employee) {
            if (!empty($employee->employer_name) && !empty($employee->position)) {
                $completeEmployees++;
            }
        }
        
        return ($completeEmployees / $totalEmployees) * 100;
    }

    /**
     * Calculate projects section completion percentage
     */
    private function calculateProjectsCompletion(Client $client): float
    {
        // Projects are optional
        return $client->projects->isEmpty() ? 100 : 
            ($client->projects->where('name', '!=', '')->count() / $client->projects->count()) * 100;
    }

    /**
     * Calculate assets section completion percentage
     */
    private function calculateAssetsCompletion(Client $client): float
    {
        // Assets are optional
        if ($client->assets->isEmpty()) {
            return 100;
        }
        
        $validAssets = $client->assets->filter(function ($asset) {
            return !empty($asset->asset_name) && 
                   !empty($asset->date_of_purchase) && 
                   !empty($asset->cost_of_acquisition);
        });
        
        return ($validAssets->count() / $client->assets->count()) * 100;
    }

    /**
     * Calculate expenses section completion percentage
     */
    private function calculateExpensesCompletion(Client $client): float
    {
        // Expenses are optional
        if ($client->expenses->isEmpty()) {
            return 100;
        }
        
        $validExpenses = $client->expenses->filter(function ($expense) {
            return !empty($expense->particulars) && 
                   !empty($expense->amount) && 
                   !empty($expense->tax_payer);
        });
        
        return ($validExpenses->count() / $client->expenses->count()) * 100;
    }

    /**
     * Store or update client information with enhanced validation and authorization
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user has client role
        if (!$user->hasRole('client')) {
            abort(403, 'Access denied. Client role required.');
        }
        
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return redirect()->back()->withErrors(['error' => 'Client record not found.']);
        }

        // Check authorization
        Gate::authorize('update', $client);

        // Validate the request data
        $validatedData = $this->validateClientInformation($request);

        try {
            DB::transaction(function () use ($client, $validatedData) {
                // Log the incoming data for debugging
                Log::info('Client information save request', [
                    'client_id' => $client->id,
                    'user_id' => Auth::id(),
                    'data_keys' => array_keys($validatedData),
                    'timestamp' => now()->toISOString()
                ]);
                
                // Update client information using service
                $this->clientInformationService->updateClient($client->id, $validatedData);
                
                // Update cache for real-time sync
                Cache::put("client_sync_{$client->id}", now(), 3600);
                
                // Log successful update
                Log::info('Client information updated successfully', [
                    'client_id' => $client->id,
                    'user_id' => Auth::id(),
                    'sections_updated' => array_keys($validatedData)
                ]);
            });

            return redirect()->back()->with('success', 'Your information has been saved successfully!');
            
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Failed to save client information', [
                'client_id' => $client->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to save information. Please try again.']);
        }
    }

    /**
     * Validate client information with comprehensive rules
     */
    private function validateClientInformation(Request $request): array
    {
        return $request->validate([
            // Personal information validation
            'personal.first_name' => 'required|string|max:50',
            'personal.middle_name' => 'nullable|string|max:50',
            'personal.last_name' => 'required|string|max:50',
            'personal.email' => 'required|email|max:255',
            'personal.phone' => 'required|string|max:20',
            'personal.ssn' => 'required|string|regex:/^\d{3}-\d{2}-\d{4}$/',
            'personal.date_of_birth' => 'required|date|before:today',
            'personal.marital_status' => 'required|in:single,married,divorced,widowed',
            'personal.occupation' => 'nullable|string|max:100',
            'personal.insurance_covered' => 'boolean',
            'personal.street_no' => 'required|string|max:255',
            'personal.apartment_no' => 'nullable|string|max:50',
            'personal.zip_code' => 'required|string|regex:/^\d{5}(-\d{4})?$/',
            'personal.city' => 'required|string|max:100',
            'personal.state' => 'required|string|max:2',
            'personal.country' => 'required|string|max:100',
            'personal.mobile_number' => 'nullable|string|max:20',
            'personal.work_number' => 'nullable|string|max:20',
            'personal.visa_status' => 'nullable|string|max:50',
            'personal.date_of_entry_us' => 'nullable|date',
            
            // Spouse information validation (conditional)
            'spouse.first_name' => 'nullable|string|max:50',
            'spouse.middle_name' => 'nullable|string|max:50',
            'spouse.last_name' => 'nullable|string|max:50',
            'spouse.email' => 'nullable|email|max:255',
            'spouse.phone' => 'nullable|string|max:20',
            'spouse.ssn' => 'nullable|string|regex:/^\d{3}-\d{2}-\d{4}$/',
            'spouse.date_of_birth' => 'nullable|date|before:today',
            'spouse.occupation' => 'nullable|string|max:100',
            
            // Employee information validation
            'employee' => 'nullable|array',
            'employee.*.employer_name' => 'nullable|string|max:100',
            'employee.*.position' => 'nullable|string|max:100',
            'employee.*.start_date' => 'nullable|date',
            'employee.*.end_date' => 'nullable|date|after_or_equal:employee.*.start_date',
            'employee.*.employment_status' => 'nullable|in:full-time,part-time,contract,temporary,terminated',
            'employee.*.salary' => 'nullable|numeric|min:0',
            'employee.*.pay_frequency' => 'nullable|in:weekly,bi-weekly,semi-monthly,monthly,annually',
            'employee.*.work_location' => 'nullable|in:office,remote,hybrid,field,multiple',
            'employee.*.notes' => 'nullable|string|max:1000',
            'employee.*.benefits' => 'nullable|array',
            
            // Projects validation
            'projects' => 'nullable|array',
            'projects.*.name' => 'nullable|string|max:200',
            'projects.*.description' => 'nullable|string|max:1000',
            'projects.*.status' => 'nullable|string|max:50',
            
            // Assets validation
            'assets' => 'nullable|array',
            'assets.*.asset_name' => 'nullable|string|max:200',
            'assets.*.date_of_purchase' => 'nullable|date',
            'assets.*.percentage_used_in_business' => 'nullable|numeric|min:0|max:100',
            'assets.*.cost_of_acquisition' => 'nullable|numeric|min:0',
            'assets.*.any_reimbursement' => 'nullable|numeric|min:0',
            
            // Expenses validation
            'expenses' => 'nullable|array',
            'expenses.*.category' => 'nullable|in:miscellaneous,residency',
            'expenses.*.particulars' => 'nullable|string|max:200',
            'expenses.*.tax_payer' => 'nullable|string|max:100',
            'expenses.*.spouse' => 'nullable|string|max:100',
            'expenses.*.amount' => 'nullable|numeric|min:0',
            'expenses.*.remarks' => 'nullable|string|max:500',
        ]);
    }

    /**
     * Auto-save client information with enhanced error handling and real-time sync
     */
    public function autoSave(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user has client role
        if (!$user->hasRole('client')) {
            return response()->json(['error' => 'Access denied. Client role required.'], 403);
        }
        
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return response()->json(['error' => 'Client record not found.'], 404);
        }

        // Check authorization
        if (!Gate::allows('update', $client)) {
            return response()->json(['error' => 'Unauthorized to update this client information.'], 403);
        }

        try {
            // Validate auto-save data (less strict than full save)
            $data = $this->validateAutoSaveData($request);
            
            if (!empty($data)) {
                DB::transaction(function () use ($client, $data) {
                    $this->clientInformationService->updateClient($client->id, $data);
                    
                    // Update real-time sync cache
                    Cache::put("client_sync_{$client->id}", now(), 3600);
                });
                
                // Calculate updated completion status
                $client->refresh();
                $completionStatus = $this->calculateCompletionStatus($client);
                
                Log::debug('Auto-save successful', [
                    'client_id' => $client->id,
                    'user_id' => $user->id,
                    'data_sections' => array_keys($data),
                    'completion' => $completionStatus['overall']
                ]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Auto-saved successfully',
                'timestamp' => now()->toISOString(),
                'completionStatus' => $completionStatus ?? null,
                'syncId' => "sync_" . time() . "_" . $client->id
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Auto-save failed', [
                'client_id' => $client->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Auto-save failed'], 500);
        }
    }

    /**
     * Validate auto-save data with relaxed rules
     */
    private function validateAutoSaveData(Request $request): array
    {
        // More lenient validation for auto-save
        return $request->validate([
            'personal' => 'nullable|array',
            'personal.first_name' => 'nullable|string|max:50',
            'personal.middle_name' => 'nullable|string|max:50',
            'personal.last_name' => 'nullable|string|max:50',
            'personal.email' => 'nullable|email|max:255',
            'personal.phone' => 'nullable|string|max:20',
            'personal.ssn' => 'nullable|string',
            'personal.date_of_birth' => 'nullable|date',
            'personal.marital_status' => 'nullable|in:single,married,divorced,widowed',
            'personal.occupation' => 'nullable|string|max:100',
            'personal.insurance_covered' => 'nullable|boolean',
            'personal.street_no' => 'nullable|string|max:255',
            'personal.apartment_no' => 'nullable|string|max:50',
            'personal.zip_code' => 'nullable|string|max:10',
            'personal.city' => 'nullable|string|max:100',
            'personal.state' => 'nullable|string|max:2',
            'personal.country' => 'nullable|string|max:100',
            'personal.mobile_number' => 'nullable|string|max:20',
            'personal.work_number' => 'nullable|string|max:20',
            'personal.visa_status' => 'nullable|string|max:50',
            'personal.date_of_entry_us' => 'nullable|date',
            
            'spouse' => 'nullable|array',
            'employee' => 'nullable|array',
            'projects' => 'nullable|array',
            'assets' => 'nullable|array',
            'expenses' => 'nullable|array',
        ]);
    }

    /**
     * Export client data in PDF format
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user has client role
        if (!$user->hasRole('client')) {
            abort(403, 'Access denied. Client role required.');
        }
        
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return redirect()->back()->withErrors(['error' => 'Client record not found.']);
        }

        // Check authorization
        Gate::authorize('export', $client);

        try {
            // Load all related data
            $client->load(['spouse', 'employees', 'projects', 'assets', 'expenses']);

            // Get export format from request (default to PDF)
            $format = $request->get('format', 'pdf');
            
            if ($format === 'pdf') {
                return $this->exportToPdf($client);
            } elseif ($format === 'excel') {
                return $this->exportToExcel($client);
            } else {
                return $this->exportToJson($client);
            }
            
        } catch (\Exception $e) {
            Log::error('Client data export failed', [
                'client_id' => $client->id,
                'user_id' => $user->id,
                'format' => $format,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->withErrors(['error' => 'Failed to export data. Please try again.']);
        }
    }

    /**
     * Export client data to PDF format
     */
    private function exportToPdf(Client $client)
    {
        // Prepare data for PDF generation
        $data = $this->prepareExportData($client);
        
        // For now, return a structured JSON response
        // In a real implementation, you would use a PDF library like DomPDF or wkhtmltopdf
        $filename = "client_information_{$client->id}_" . now()->format('Y-m-d') . ".pdf";
        
        // Create a simple HTML structure for PDF generation
        $html = $this->generatePdfHtml($data);
        
        // Return as downloadable response
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Export client data to Excel format
     */
    private function exportToExcel(Client $client)
    {
        $data = $this->prepareExportData($client);
        $filename = "client_information_{$client->id}_" . now()->format('Y-m-d') . ".json";
        
        // For now, return JSON. In real implementation, use Laravel Excel
        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Export client data to JSON format
     */
    private function exportToJson(Client $client)
    {
        $data = $this->prepareExportData($client);
        $filename = "client_information_{$client->id}_" . now()->format('Y-m-d') . ".json";
        
        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Prepare client data for export
     */
    private function prepareExportData(Client $client): array
    {
        return [
            'export_info' => [
                'generated_at' => now()->toISOString(),
                'client_id' => $client->id,
                'export_version' => '1.0'
            ],
            'personal_information' => [
                'name' => trim($client->first_name . ' ' . ($client->middle_name ?? '') . ' ' . $client->last_name),
                'email' => $client->email,
                'phone' => $client->phone,
                'date_of_birth' => $client->date_of_birth?->format('F j, Y'),
                'marital_status' => ucfirst($client->marital_status ?? 'Not specified'),
                'occupation' => $client->occupation ?? 'Not specified',
                'insurance_covered' => $client->insurance_covered ? 'Yes' : 'No',
                'address' => [
                    'street' => $client->street_no . ($client->apartment_no ? ', ' . $client->apartment_no : ''),
                    'city' => $client->city,
                    'state' => $client->state,
                    'zip_code' => $client->zip_code,
                    'country' => $client->country ?? 'United States'
                ],
                'contact' => [
                    'mobile' => $client->mobile_number,
                    'work' => $client->work_number
                ],
                'immigration' => [
                    'visa_status' => $client->visa_status,
                    'date_of_entry_us' => $client->date_of_entry_us?->format('F j, Y')
                ]
            ],
            'spouse_information' => $client->spouse ? [
                'name' => trim($client->spouse->first_name . ' ' . ($client->spouse->middle_name ?? '') . ' ' . $client->spouse->last_name),
                'email' => $client->spouse->email,
                'phone' => $client->spouse->phone,
                'date_of_birth' => $client->spouse->date_of_birth?->format('F j, Y'),
                'occupation' => $client->spouse->occupation ?? 'Not specified'
            ] : null,
            'employment_information' => $client->employees->map(function ($employee) {
                return [
                    'employer' => $employee->employer_name,
                    'position' => $employee->position,
                    'employment_period' => [
                        'start_date' => $employee->start_date?->format('F j, Y'),
                        'end_date' => $employee->end_date?->format('F j, Y') ?? 'Current'
                    ],
                    'status' => $employee->employment_status,
                    'compensation' => [
                        'salary' => $employee->salary ? '$' . number_format($employee->salary, 2) : 'Not specified',
                        'frequency' => $employee->pay_frequency
                    ],
                    'work_location' => $employee->work_location,
                    'benefits' => $employee->benefits ?? [],
                    'notes' => $employee->notes
                ];
            })->toArray(),
            'projects' => $client->projects->toArray(),
            'assets' => $client->assets->map(function ($asset) {
                return [
                    'name' => $asset->asset_name,
                    'purchase_date' => $asset->date_of_purchase?->format('F j, Y'),
                    'business_use_percentage' => $asset->percentage_used_in_business . '%',
                    'cost' => '$' . number_format($asset->cost_of_acquisition, 2),
                    'reimbursement' => $asset->any_reimbursement ? '$' . number_format($asset->any_reimbursement, 2) : 'None'
                ];
            })->toArray(),
            'expenses' => $client->expenses->map(function ($expense) {
                return [
                    'category' => ucfirst($expense->category),
                    'particulars' => $expense->particulars,
                    'tax_payer' => $expense->tax_payer,
                    'spouse' => $expense->spouse,
                    'amount' => '$' . number_format($expense->amount, 2),
                    'remarks' => $expense->remarks
                ];
            })->toArray()
        ];
    }

    /**
     * Generate HTML for PDF export
     */
    private function generatePdfHtml(array $data): string
    {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Client Information Export</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .section { margin-bottom: 25px; }
                .section-title { font-size: 18px; font-weight: bold; color: #333; border-bottom: 2px solid #007bff; padding-bottom: 5px; margin-bottom: 15px; }
                .field { margin-bottom: 8px; }
                .field-label { font-weight: bold; display: inline-block; width: 150px; }
                .field-value { display: inline-block; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Client Information Export</h1>
                <p>Generated on: ' . $data['export_info']['generated_at'] . '</p>
                <p>Client ID: ' . $data['export_info']['client_id'] . '</p>
            </div>';

        // Personal Information Section
        $personal = $data['personal_information'];
        $html .= '<div class="section">
            <div class="section-title">Personal Information</div>
            <div class="field"><span class="field-label">Name:</span><span class="field-value">' . $personal['name'] . '</span></div>
            <div class="field"><span class="field-label">Email:</span><span class="field-value">' . $personal['email'] . '</span></div>
            <div class="field"><span class="field-label">Phone:</span><span class="field-value">' . $personal['phone'] . '</span></div>
            <div class="field"><span class="field-label">Date of Birth:</span><span class="field-value">' . $personal['date_of_birth'] . '</span></div>
            <div class="field"><span class="field-label">Marital Status:</span><span class="field-value">' . $personal['marital_status'] . '</span></div>
            <div class="field"><span class="field-label">Occupation:</span><span class="field-value">' . $personal['occupation'] . '</span></div>
            <div class="field"><span class="field-label">Insurance:</span><span class="field-value">' . $personal['insurance_covered'] . '</span></div>
        </div>';

        // Address Section
        $address = $personal['address'];
        $html .= '<div class="section">
            <div class="section-title">Address Information</div>
            <div class="field"><span class="field-label">Street:</span><span class="field-value">' . $address['street'] . '</span></div>
            <div class="field"><span class="field-label">City:</span><span class="field-value">' . $address['city'] . '</span></div>
            <div class="field"><span class="field-label">State:</span><span class="field-value">' . $address['state'] . '</span></div>
            <div class="field"><span class="field-label">ZIP Code:</span><span class="field-value">' . $address['zip_code'] . '</span></div>
            <div class="field"><span class="field-label">Country:</span><span class="field-value">' . $address['country'] . '</span></div>
        </div>';

        // Add other sections as needed...
        
        $html .= '</body></html>';
        
        return $html;
    }

    /**
     * Request review from tax professional with enhanced features
     */
    public function requestReview(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user has client role
        if (!$user->hasRole('client')) {
            abort(403, 'Access denied. Client role required.');
        }
        
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return redirect()->back()->withErrors(['error' => 'Client record not found.']);
        }

        // Check authorization
        Gate::authorize('requestReview', $client);

        // Validate request data
        $validatedData = $request->validate([
            'sections' => 'nullable|array',
            'sections.*' => 'in:personal,spouse,employee,projects,assets,expenses',
            'message' => 'nullable|string|max:1000',
            'priority' => 'nullable|in:low,normal,high',
        ]);

        try {
            DB::transaction(function () use ($client, $validatedData, $user) {
                // Update client status to indicate review requested
                $client->update([
                    'status' => 'review_requested',
                    'review_requested_at' => now(),
                    'review_sections' => $validatedData['sections'] ?? null,
                    'review_message' => $validatedData['message'] ?? null,
                    'review_priority' => $validatedData['priority'] ?? 'normal'
                ]);

                // Log the review request
                Log::info('Client review requested', [
                    'client_id' => $client->id,
                    'user_id' => $user->id,
                    'sections' => $validatedData['sections'] ?? 'all',
                    'priority' => $validatedData['priority'] ?? 'normal',
                    'message_length' => strlen($validatedData['message'] ?? ''),
                    'timestamp' => now()->toISOString()
                ]);

                // Create notification for assigned tax professional
                $this->createReviewNotification($client, $validatedData);
                
                // Update cache for real-time sync
                Cache::put("client_review_request_{$client->id}", [
                    'status' => 'pending',
                    'requested_at' => now()->toISOString(),
                    'sections' => $validatedData['sections'] ?? null
                ], 86400); // 24 hours
            });

            return redirect()->back()->with('success', 'Review request sent to your tax professional! You will be notified when the review is complete.');
            
        } catch (\Exception $e) {
            Log::error('Failed to request review', [
                'client_id' => $client->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->withErrors(['error' => 'Failed to send review request. Please try again.']);
        }
    }

    /**
     * Create notification for tax professional about review request
     */
    private function createReviewNotification(Client $client, array $requestData): void
    {
        // In a real implementation, this would create a notification record
        // and potentially send an email to the assigned tax professional
        
        $notificationData = [
            'type' => 'client_review_request',
            'client_id' => $client->id,
            'client_name' => trim($client->first_name . ' ' . $client->last_name),
            'client_email' => $client->email,
            'sections_requested' => $requestData['sections'] ?? ['all'],
            'message' => $requestData['message'] ?? null,
            'priority' => $requestData['priority'] ?? 'normal',
            'requested_at' => now()->toISOString()
        ];
        
        // Log notification creation for now
        Log::info('Review notification created', $notificationData);
        
        // TODO: Implement actual notification system
        // - Create database notification record
        // - Send email to assigned tax professional
        // - Create in-app notification
    }

    /**
     * Mark sections as complete for review
     */
    public function markSectionComplete(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('client')) {
            abort(403, 'Access denied. Client role required.');
        }
        
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return response()->json(['error' => 'Client record not found.'], 404);
        }

        Gate::authorize('update', $client);

        $validatedData = $request->validate([
            'section' => 'required|in:personal,spouse,employee,projects,assets,expenses',
            'completed' => 'required|boolean'
        ]);

        try {
            // Get current completion status
            $completionStatus = $client->section_completion_status ?? [];
            
            // Update section completion
            $completionStatus[$validatedData['section']] = [
                'completed' => $validatedData['completed'],
                'completed_at' => $validatedData['completed'] ? now()->toISOString() : null,
                'completed_by' => $user->id
            ];
            
            $client->update(['section_completion_status' => $completionStatus]);
            
            // Update cache
            Cache::put("client_completion_{$client->id}", $completionStatus, 3600);
            
            return response()->json([
                'success' => true,
                'message' => 'Section completion status updated',
                'completionStatus' => $completionStatus
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to update section completion', [
                'client_id' => $client->id,
                'section' => $validatedData['section'],
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Failed to update completion status'], 500);
        }
    }

    /**
     * Get review status for client
     */
    public function getReviewStatus()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('client')) {
            abort(403, 'Access denied. Client role required.');
        }
        
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return response()->json(['error' => 'Client record not found.'], 404);
        }

        Gate::authorize('view', $client);

        $reviewStatus = Cache::get("client_review_request_{$client->id}", [
            'status' => 'none',
            'requested_at' => null,
            'sections' => null
        ]);

        return response()->json([
            'reviewStatus' => $reviewStatus,
            'clientStatus' => $client->status,
            'lastUpdated' => $client->updated_at->toISOString(),
            'completionStatus' => $this->calculateCompletionStatus($client)
        ]);
    }


}