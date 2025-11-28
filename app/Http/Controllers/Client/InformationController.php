<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientInformationService;
use App\Services\ErrorHandlingService;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Enums\VisaStatus;
use Inertia\Inertia;

class InformationController extends Controller
{
    public function __construct(
        private ClientInformationService $clientInformationService,
        private AdminNotificationService $adminNotificationService
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
            'visaStatusOptions' => VisaStatus::options(),
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
        $client = $user->client;
        
        if (!$client) {
            // Create a basic client record with minimal required fields
            $client = DB::transaction(function () use ($user) {
                return Client::create([
                    'user_id' => $user->id,
                    'status' => 'active'
                ]);
            });
            
            Log::info('Created new client record information', [
                'client_id' => $client->id,
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }
        
        return $client;
    }

    /**
     * Mask SSN for security (show only last 4 digits)
     */
    private function maskSSN(?string $ssn): string
    {
        if (!$ssn) {
            return '';
        }
        
        // Remove any non-digit characters
        $digits = preg_replace('/\D/', '', $ssn);
        
        if (strlen($digits) === 9) {
            // Show only last 4 digits: ***-**-1234
            return '***-**-' . substr($digits, -4);
        } elseif (strlen($digits) >= 4) {
            // For partial SSNs, mask what we can
            $lastFour = substr($digits, -4);
            $maskedLength = max(0, strlen($digits) - 4);
            $masked = str_repeat('*', $maskedLength);
            
            if (strlen($digits) >= 7) {
                // Format as ***-**-1234
                return substr($masked, 0, 3) . '-' . substr($masked, 3, 2) . '-' . $lastFour;
            } elseif (strlen($digits) >= 5) {
                // Format as ***-**
                return substr($masked, 0, 3) . '-' . substr($masked, 3) . $lastFour;
            } else {
                // Just show masked + last digits
                return $masked . $lastFour;
            }
        }
        
        // For very short inputs, don't mask
        return $ssn;
    }

    /**
     * Prepare client data for form with enhanced structure
     */
    private function prepareClientDataForForm(Client $client): array
    {
        return [
            'personal' => [
                'first_name' => $client->user->first_name ?? '',
                'middle_name' => $client->user->middle_name ?? '',
                'last_name' => $client->user->last_name ?? '',
                'email' => $client->user->email ?? '',
                'phone' => $client->phone ?? '',
                'ssn' => $this->maskSSN($client->ssn),
                'date_of_birth' => $client->date_of_birth ?? '',
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
                'date_of_entry_us' => $client->date_of_entry_us ?? '',
            ],
            'spouse' => $client->spouse ? [
                'first_name' => $client->spouse->first_name,
                'middle_name' => $client->spouse->middle_name,
                'last_name' => $client->spouse->last_name,
                'email' => $client->spouse->email,
                'phone' => $client->spouse->phone,
                'ssn' => $this->maskSSN($client->spouse->ssn),
                'date_of_birth' => $client->spouse->date_of_birth ? $client->spouse->date_of_birth->format('Y-m-d') : '',
                'occupation' => $client->spouse->occupation,
            ] : [],
            'employee' => $client->employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'employerName' => $employee->employer_name,
                    'position' => $employee->job_title,
                    'startDate' => $employee->start_date?->format('Y-m-d'),
                    'endDate' => $employee->end_date?->format('Y-m-d'),
                    'employmentType' => $employee->employment_type,
                    'salary' => $employee->annual_salary,
                    'payFrequency' => $employee->pay_frequency,
                    'workLocation' => $employee->work_location,
                    'notes' => $employee->work_description,
                    'employerAddress' => $employee->employer_address,
                    'employerCity' => $employee->employer_city,
                    'employerState' => $employee->employer_state,
                    'employerZipCode' => $employee->employer_zip_code,
                    'benefits' => $employee->benefits ?? [
                        'healthInsurance' => false,
                        'retirementPlan' => false,
                        'dentalInsurance' => false,
                        'visionInsurance' => false
                    ],
                ];
            })->toArray(),
            'projects' => $client->projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'project_name' => $project->project_name,
                    'project_description' => $project->project_description,
                    'project_type' => $project->project_type,
                    'status' => $project->status,
                    'priority' => 'medium', // Default priority since it's not in database
                    'start_date' => $project->start_date?->format('Y-m-d'),
                    'due_date' => $project->due_date?->format('Y-m-d'),
                    'completion_date' => $project->completion_date?->format('Y-m-d'),
                    'estimated_hours' => $project->estimated_hours,
                    'actual_hours' => $project->actual_hours,
                    'notes' => $project->notes,
                ];
            })->toArray(),
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
            if (!empty($employee->employer_name) && !empty($employee->job_title)) {
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
        
        $client = $user->client;

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
                
                // // Log successful update
                // Log::info('Client information updated successfully', [
                //     'client_id' => $client->id,
                //     'user_id' => Auth::id(),
                //     'sections_updated' => array_keys($validatedData)
                // ]);
            });

            return redirect()->back()->with('success', 'Your information has been saved successfully!');
            
        } catch (ValidationException $e) {
            $errorData = \App\Services\ErrorHandlingService::formatValidationErrors($e);
            return redirect()->back()->withErrors($e->errors())->with('error_details', $errorData['error']);
        } catch (\Exception $e) {
            $errorData = \App\Services\ErrorHandlingService::handleWebError($e, [
                'client_id' => $client->id,
                'user_id' => Auth::id(),
                'action' => 'save_client_information'
            ]);
            return redirect()->back()->withErrors($errorData);
        }
    }

    /**
     * Validate client information with comprehensive rules
     */
    private function validateClientInformation(Request $request): array
    {
        //  Log::debug('employee data', [
        //     'request' => $request
        //     ]);
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
            'personal.visa_status' => ['nullable', Rule::enum(VisaStatus::class)],
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
            'employee.*.employerName' => 'nullable|string|max:100',
            'employee.*.position' => 'nullable|string|max:100',
            'employee.*.startDate' => 'nullable|date',
            'employee.*.endDate' => 'nullable|date|after_or_equal:employee.*.startDate',
            'employee.*.employmentType' => 'nullable|in:full-time,part-time,contract,temporary,terminated',
            'employee.*.salary' => 'nullable|numeric|min:0',
            'employee.*.payFrequency' => 'nullable|in:weekly,bi-weekly,semi-monthly,monthly,annually',
            'employee.*.workLocation' => 'nullable|in:office,remote,hybrid,field,multiple',
            'employee.*.notes' => 'nullable|string|max:1000',
            'employee.*.benefits' => 'nullable|array',
            
            // Projects validation
            'projects' => 'nullable|array',
            'projects.*.id' => 'nullable|integer',
            'projects.*.name' => 'nullable|string|max:200',
            'projects.*.project_name' => 'nullable|string|max:200',
            'projects.*.description' => 'nullable|string|max:1000',
            'projects.*.project_description' => 'nullable|string|max:1000',
            'projects.*.type' => 'nullable|string|max:50',
            'projects.*.project_type' => 'nullable|string|max:50',
            'projects.*.status' => 'nullable|string|max:50',
            'projects.*.startDate' => 'nullable|date',
            'projects.*.start_date' => 'nullable|date',
            'projects.*.dueDate' => 'nullable|date',
            'projects.*.due_date' => 'nullable|date',
            'projects.*.completionDate' => 'nullable|date',
            'projects.*.completion_date' => 'nullable|date',
            'projects.*.estimatedHours' => 'nullable|numeric|min:0',
            'projects.*.estimated_hours' => 'nullable|numeric|min:0',
            'projects.*.actualHours' => 'nullable|numeric|min:0',
            'projects.*.actual_hours' => 'nullable|numeric|min:0',
            'projects.*.notes' => 'nullable|string|max:1000',
            
            // Assets validation
            'assets' => 'nullable|array',
            'assets.*.id' => 'nullable|integer',
            'assets.*.asset_name' => 'nullable|string|max:200',
            'assets.*.asset_type' => 'nullable|string|max:50',
            'assets.*.date_of_purchase' => 'nullable|date',
            'assets.*.percentage_used_in_business' => 'nullable|numeric|min:0|max:100',
            'assets.*.cost_of_acquisition' => 'nullable|numeric|min:0',
            'assets.*.any_reimbursement' => 'nullable|numeric|min:0',
            'assets.*.current_value' => 'nullable|numeric|min:0',
            'assets.*.depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'assets.*.description' => 'nullable|string|max:1000',
            
            // Expenses validation
            'expenses' => 'nullable|array',
            'expenses.*.category' => 'nullable|string|max:50',
            'expenses.*.particulars' => 'nullable|string|max:200',
            'expenses.*.tax_payer' => 'nullable|string|max:100',
            'expenses.*.taxPayer' => 'nullable|string|max:100',
            'expenses.*.spouse' => 'nullable|string|max:100',
            'expenses.*.amount' => 'nullable|numeric|min:0',
            'expenses.*.date' => 'nullable|date',
            'expenses.*.expense_date' => 'nullable|date',
            'expenses.*.deductible' => 'nullable|boolean',
            'expenses.*.is_deductible' => 'nullable|boolean',
            'expenses.*.deductible_percentage' => 'nullable|numeric|min:0|max:100',
            'expenses.*.deductiblePercentage' => 'nullable|numeric|min:0|max:100',
            'expenses.*.receipt_number' => 'nullable|string|max:255',
            'expenses.*.receiptNumber' => 'nullable|string|max:255',
            'expenses.*.remarks' => 'nullable|string|max:500',
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
        
        $client = $user->client;

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
        // Update last export timestamp
        $client->update(['last_export_at' => now()]);
        
        // Load all related data for the PDF
        $client->load(['spouse', 'employees', 'projects', 'assets', 'expenses', 'user']);
        
        // Generate filename
        $filename = "client_information_{$client->id}_" . now()->format('Y-m-d') . ".pdf";
        
        // Generate PDF using DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.client-information', compact('client'));
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Return PDF download
        return $pdf->download($filename);
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
                'date_of_birth' => $client->date_of_birth ? date('F j, Y', strtotime($client->date_of_birth)) : 'Not Specified2',
                'marital_status' => ucfirst($client->marital_status ?? 'Not Specified2'),
                'occupation' => $client->occupation ?? 'Not Specified2',
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
                    'date_of_entry_us' => $client->date_of_entry_us ? \Carbon\Carbon::createFromFormat('Y-m-d', $client->date_of_entry_us)->format('F j, Y') : null
                ]
            ],
            'spouse_information' => $client->spouse ? [
                'name' => trim($client->spouse->first_name . ' ' . ($client->spouse->middle_name ?? '') . ' ' . $client->spouse->last_name),
                'email' => $client->spouse->email,
                'phone' => $client->spouse->phone,
                'date_of_birth' => $client->spouse->date_of_birth ? date('F j, Y', strtotime($client->spouse->date_of_birth)) : 'Not Specified2',
                'occupation' => $client->spouse->occupation ?? 'Not Specified2'
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
                        'salary' => $employee->salary ? '$' . number_format($employee->salary, 2) : 'Not Specified2',
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
     * Request review from tax professional with enhanced features
     */
    public function requestReview(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user has client role
        if (!$user->hasRole('client')) {
            abort(403, 'Access denied. Client role required.');
        }
        
        $client = $user->client;

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
        // Notify all admins about the review request
        $this->adminNotificationService->notifyReviewRequested($client, $requestData);
        
        // Also send individual notification to assigned tax professional if exists
        $taxProfessional = $client->user ?? User::where('role', 'admin')->first();
        
        if ($taxProfessional) {
            // Send individual notification
            $taxProfessional->notify(new \App\Notifications\ClientReviewRequestNotification(
                $client,
                $requestData['sections'] ?? [],
                $requestData['message'] ?? null,
                $requestData['priority'] ?? 'normal'
            ));
            
            Log::info('Review notification sent', [
                'client_id' => $client->id,
                'client_name' => $client->full_name,
                'tax_professional_id' => $taxProfessional->id,
                'tax_professional_email' => $taxProfessional->email,
                'sections' => $requestData['sections'] ?? [],
                'priority' => $requestData['priority'] ?? 'normal'
            ]);
        } else {
            Log::warning('No assigned tax professional found, but admin notifications sent', [
                'client_id' => $client->id,
                'client_name' => $client->full_name
            ]);
        }
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
        
        $client = $user->client;

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
        
        $client = $user->client;

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
        
        $client = $user->client;

        if (!$client) {
            return response()->json(['error' => 'Client record not found.'], 404);
        }

        // Check authorization
        if (!Gate::allows('update', $client)) {
            return response()->json(['error' => 'Unauthorized to update this client information.'], 403);
        }

        // Log the complete request data for debugging
        Log::debug('Auto-save request received 222', [
            'client_id' => $client->id,
            'user_id' => $user->id,
            'request_data' => $request->all(),
            'employee_data' => $request->get('employee'),
            'has_employee_data' => !empty($request->get('employee'))
        ]);

        try {
            // Validate auto-save data (less strict than full save)
            $data = $this->validateAutoSaveData($request);

            //  Log::debug('Auto-save data validated', [
            //        $data['employee']
            //     ]);
            
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
            $errorData = \App\Services\ErrorHandlingService::formatValidationErrors($e);
            return response()->json($errorData, 422);
        } catch (\Exception $e) {
            $errorData = \App\Services\ErrorHandlingService::handleApiError($e, [
                'client_id' => $client->id,
                'user_id' => $user->id,
                'action' => 'auto_save_client_information'
            ]);
            return response()->json($errorData, 500);
        }
    }

    /**
     * Validate auto-save data with relaxed rules
     */
    private function validateAutoSaveData(Request $request): array
    {
         Log::info('Auto-save request22', [$request]);

        //  More lenient validation for auto-save
        return $request->validate([
            'personal' => 'nullable|array',
            'personal.first_name' => 'nullable|string|max:50',
            'personal.middle_name' => 'nullable|string|max:50',
            'personal.last_name' => 'nullable|string|max:50',
            'personal.email' => 'nullable|email|max:255',
            'personal.phone' => 'nullable|string|max:20',
            'personal.ssn' => 'nullable|string',
            'personal.date_of_birth' => 'nullable|date|date_format:Y-m-d',
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
            'personal.visa_status' => ['nullable', Rule::enum(VisaStatus::class)],
            'personal.date_of_entry_us' => 'nullable|date|date_format:Y-m-d',
            
            'spouse' => 'nullable|array',
            'spouse.first_name' => 'nullable|string|max:50',
            'spouse.middle_name' => 'nullable|string|max:50',
            'spouse.last_name' => 'nullable|string|max:50',
            'spouse.email' => 'nullable|email|max:255',
            'spouse.phone' => 'nullable|string|max:20',
            'spouse.ssn' => 'nullable|string',
            'spouse.date_of_birth' => 'nullable|date|date_format:Y-m-d',
            'spouse.occupation' => 'nullable|string|max:100',
            
            'employee' => 'nullable|array',
            'employee.*.id' => 'nullable|integer',
            'employee.*.employer_name' => 'nullable|string|max:100',
            'employee.*.position' => 'nullable|string|max:100',
            'employee.*.start_date' => 'nullable|date|date_format:Y-m-d',
            'employee.*.end_date' => 'nullable|date|date_format:Y-m-d',
            'employee.*.employment_type' => 'nullable|in:full-time,part-time,contract,temporary,terminated',
            'employee.*.salary' => 'nullable|numeric|min:0',
            'employee.*.pay_frequency' => 'nullable|in:weekly,bi-weekly,semi-monthly,monthly,annually',
            'employee.*.work_location' => 'nullable|in:office,remote,hybrid,field,multiple',
            'employee.*.notes' => 'nullable|string|max:1000',
            'employee.*.employer_address' => 'nullable|string|max:255',
            'employee.*.employer_city' => 'nullable|string|max:100',
            'employee.*.employer_state' => 'nullable|string|max:2',
            'employee.*.employer_zip_code' => 'nullable|string|max:10',
            'employee.*.benefits' => 'nullable|array',
            'employee.*.workLocation' => 'nullable|in:office,remote,hybrid,field,multiple',
            'employee.*.notes' => 'nullable|string|max:1000',
            'employee.*.benefits' => 'nullable|array',
            
            'projects' => 'nullable|array',
            'projects.*.id' => 'nullable|integer',
            'projects.*.name' => 'nullable|string|max:200',
            'projects.*.project_name' => 'nullable|string|max:200',
            'projects.*.description' => 'nullable|string|max:1000',
            'projects.*.project_description' => 'nullable|string|max:1000',
            'projects.*.status' => 'nullable|string|max:50',
            'projects.*.type' => 'nullable|string|max:50',
            'projects.*.project_type' => 'nullable|string|max:50',
            'projects.*.startDate' => 'nullable|date|date_format:Y-m-d',
            'projects.*.start_date' => 'nullable|date|date_format:Y-m-d',
            'projects.*.dueDate' => 'nullable|date|date_format:Y-m-d',
            'projects.*.due_date' => 'nullable|date|date_format:Y-m-d',
            'projects.*.completionDate' => 'nullable|date|date_format:Y-m-d',
            'projects.*.completion_date' => 'nullable|date|date_format:Y-m-d',
            'projects.*.estimatedHours' => 'nullable|numeric|min:0',
            'projects.*.estimated_hours' => 'nullable|numeric|min:0',
            'projects.*.actualHours' => 'nullable|numeric|min:0',
            'projects.*.actual_hours' => 'nullable|numeric|min:0',
            'projects.*.notes' => 'nullable|string|max:1000',
            
            'assets' => 'nullable|array',
            'assets.*.id' => 'nullable|integer',
            'assets.*.asset_name' => 'nullable|string|max:200',
            'assets.*.asset_type' => 'nullable|string|max:50',
            'assets.*.date_of_purchase' => 'nullable|date|date_format:Y-m-d',
            'assets.*.percentage_used_in_business' => 'nullable|numeric|min:0|max:100',
            'assets.*.cost_of_acquisition' => 'nullable|numeric|min:0',
            'assets.*.any_reimbursement' => 'nullable|numeric|min:0',
            'assets.*.current_value' => 'nullable|numeric|min:0',
            'assets.*.depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'assets.*.description' => 'nullable|string|max:1000',
            
            'expenses' => 'nullable|array',
            'expenses.*.category' => 'nullable|string|max:50',
            'expenses.*.particulars' => 'nullable|string|max:200',
            'expenses.*.tax_payer' => 'nullable|string|max:100',
            'expenses.*.spouse' => 'nullable|string|max:100',
            'expenses.*.amount' => 'nullable|numeric|min:0',
            'expenses.*.remarks' => 'nullable|string|max:500',
        ]);
    }


}