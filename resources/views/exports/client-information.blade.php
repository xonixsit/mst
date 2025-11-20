<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Client Information Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin: 0;
        }
        
        .header p {
            color: #666;
            margin: 5px 0;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #f3f4f6;
            color: #1f2937;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-left: 4px solid #2563eb;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 8px 15px 8px 0;
            width: 30%;
            vertical-align: top;
            color: #374151;
        }
        
        .info-value {
            display: table-cell;
            padding: 8px 0;
            vertical-align: top;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .table th {
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .table td {
            border: 1px solid #d1d5db;
            padding: 8px;
            vertical-align: top;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        
        .empty-state {
            color: #9ca3af;
            font-style: italic;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MySuperTax - Client Information Export</h1>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>Client ID: {{ $client->id }}</p>
    </div>

    <!-- Personal Information -->
    <div class="section">
        <div class="section-title">Personal Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value">{{ $client->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $client->email ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $client->phone ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Mobile Number:</div>
                <div class="info-value">{{ $client->getDecryptedAttribute('mobile_number') ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Work Number:</div>
                <div class="info-value">{{ $client->getDecryptedAttribute('work_number') ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Social Security Number:</div>
                <div class="info-value">{{ $client->getDecryptedAttribute('ssn') ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date of Birth:</div>
                <div class="info-value">{{ $client->getDecryptedAttribute('date_of_birth') ? \Carbon\Carbon::createFromFormat('Y-m-d', $client->getDecryptedAttribute('date_of_birth'))->format('F j, Y') : 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Marital Status:</div>
                <div class="info-value">{{ ucfirst($client->marital_status ?? 'Not specified') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Occupation:</div>
                <div class="info-value">{{ $client->occupation ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Insurance Coverage:</div>
                <div class="info-value">{{ $client->insurance_covered ? 'Yes' : 'No' }}</div>
            </div>
        </div>
    </div>

    <!-- Address Information -->
    <div class="section">
        <div class="section-title">Address Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Street Address:</div>
                <div class="info-value">{{ $client->street_no ?? 'Not provided' }}</div>
            </div>
            @if($client->apartment_no)
            <div class="info-row">
                <div class="info-label">Apartment/Unit:</div>
                <div class="info-value">{{ $client->apartment_no }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">City:</div>
                <div class="info-value">{{ $client->city ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">State:</div>
                <div class="info-value">{{ $client->state ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">ZIP Code:</div>
                <div class="info-value">{{ $client->zip_code ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Country:</div>
                <div class="info-value">{{ $client->country ?? 'Not provided' }}</div>
            </div>
        </div>
    </div>

    <!-- Immigration Information -->
    @if($client->visa_status || $client->getDecryptedAttribute('date_of_entry_us'))
    <div class="section">
        <div class="section-title">Immigration Information</div>
        <div class="info-grid">
            @if($client->visa_status)
            <div class="info-row">
                <div class="info-label">Visa Status:</div>
                <div class="info-value">{{ $client->visa_status->label() ?? $client->visa_status }}</div>
            </div>
            @endif
            @if($client->getDecryptedAttribute('date_of_entry_us'))
            <div class="info-row">
                <div class="info-label">Date of Entry to US:</div>
                <div class="info-value">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $client->getDecryptedAttribute('date_of_entry_us'))->format('F j, Y') }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Spouse Information -->
    @if($client->spouse)
    <div class="section">
        <div class="section-title">Spouse Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value">{{ $client->spouse->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $client->spouse->email ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $client->spouse->phone ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Social Security Number:</div>
                <div class="info-value">{{ $client->spouse->ssn ?? 'Not provided' }}</div>
            </div>
            @if($client->spouse->date_of_birth)
            <div class="info-row">
                <div class="info-label">Date of Birth:</div>
                <div class="info-value">{{ $client->spouse->date_of_birth->format('F j, Y') }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Occupation:</div>
                <div class="info-value">{{ $client->spouse->occupation ?? 'Not provided' }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Employment Information -->
    @if($client->employees->count() > 0)
    <div class="section">
        <div class="section-title">Employment Information</div>
        @foreach($client->employees as $employee)
        <div class="info-grid" style="margin-bottom: 15px;">
            <div class="info-row">
                <div class="info-label">Employer:</div>
                <div class="info-value">{{ $employee->employer_name ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Position:</div>
                <div class="info-value">{{ $employee->position ?? 'Not provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Start Date:</div>
                <div class="info-value">{{ $employee->start_date ? $employee->start_date->format('F j, Y') : 'Not provided' }}</div>
            </div>
            @if($employee->end_date)
            <div class="info-row">
                <div class="info-label">End Date:</div>
                <div class="info-value">{{ $employee->end_date->format('F j, Y') }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Annual Salary:</div>
                <div class="info-value">${{ number_format($employee->annual_salary ?? 0, 2) }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Projects -->
    @if($client->projects->count() > 0)
    <div class="section">
        <div class="section-title">Projects</div>
        @foreach($client->projects as $project)
        <div class="info-grid" style="margin-bottom: 20px; border: 1px solid #e5e7eb; padding: 15px; border-radius: 5px;">
            <div class="info-row">
                <div class="info-label">Project Name:</div>
                <div class="info-value">{{ $project->project_name ?? 'Unnamed Project' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Description:</div>
                <div class="info-value">{{ $project->project_description ?? 'No description provided' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Project Type:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $project->project_type ?? 'Not specified')) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span style="padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; 
                        @if($project->status === 'completed') background-color: #d1fae5; color: #065f46;
                        @elseif($project->status === 'in_progress') background-color: #dbeafe; color: #1e40af;
                        @elseif($project->status === 'pending') background-color: #fef3c7; color: #92400e;
                        @else background-color: #fee2e2; color: #991b1b; @endif">
                        {{ ucfirst(str_replace('_', ' ', $project->status ?? 'Unknown')) }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Start Date:</div>
                <div class="info-value">{{ $project->start_date ? $project->start_date->format('F j, Y') : 'Not set' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Due Date:</div>
                <div class="info-value">{{ $project->due_date ? $project->due_date->format('F j, Y') : 'Not set' }}</div>
            </div>
            @if($project->completion_date)
            <div class="info-row">
                <div class="info-label">Completion Date:</div>
                <div class="info-value">{{ $project->completion_date->format('F j, Y') }}</div>
            </div>
            @endif
            @if($project->estimated_hours)
            <div class="info-row">
                <div class="info-label">Estimated Hours:</div>
                <div class="info-value">{{ number_format($project->estimated_hours, 1) }} hours</div>
            </div>
            @endif
            @if($project->actual_hours)
            <div class="info-row">
                <div class="info-label">Actual Hours:</div>
                <div class="info-value">{{ number_format($project->actual_hours, 1) }} hours</div>
            </div>
            @endif
            @if($project->estimated_hours && $project->actual_hours)
            <div class="info-row">
                <div class="info-label">Hours Variance:</div>
                <div class="info-value">
                    @php $variance = $project->actual_hours - $project->estimated_hours; @endphp
                    <span style="color: {{ $variance > 0 ? '#dc2626' : '#059669' }};">
                        {{ $variance > 0 ? '+' : '' }}{{ number_format($variance, 1) }} hours
                        ({{ $variance > 0 ? 'Over' : 'Under' }} estimate)
                    </span>
                </div>
            </div>
            @endif
            @if($project->notes)
            <div class="info-row">
                <div class="info-label">Notes:</div>
                <div class="info-value">{{ $project->notes }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Progress:</div>
                <div class="info-value">
                    @php 
                        $progress = match($project->status) {
                            'pending' => 0,
                            'in_progress' => 50,
                            'completed' => 100,
                            'cancelled' => 0,
                            default => 0,
                        };
                    @endphp
                    <div style="width: 100px; height: 8px; background-color: #e5e7eb; border-radius: 4px; display: inline-block; position: relative;">
                        <div style="width: {{ $progress }}%; height: 100%; background-color: {{ $progress === 100 ? '#10b981' : ($progress > 0 ? '#3b82f6' : '#6b7280') }}; border-radius: 4px;"></div>
                    </div>
                    <span style="margin-left: 8px; font-size: 12px;">{{ $progress }}%</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Assets -->
    @if($client->assets->count() > 0)
    <div class="section">
        <div class="section-title">Assets</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Asset Type</th>
                    <th>Description</th>
                    <th>Value</th>
                    <th>Purchase Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->assets as $asset)
                <tr>
                    <td>{{ ucfirst($asset->asset_type ?? 'Unknown') }}</td>
                    <td>{{ $asset->description ?? 'No description' }}</td>
                    <td>${{ number_format($asset->value ?? 0, 2) }}</td>
                    <td>{{ $asset->date_of_purchase ? $asset->date_of_purchase->format('M j, Y') : 'Not provided' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Expenses -->
    @if($client->expenses->count() > 0)
    <div class="section">
        <div class="section-title">Expenses</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->expenses as $expense)
                <tr>
                    <td>{{ ucfirst($expense->category ?? 'Uncategorized') }}</td>
                    <td>{{ $expense->description ?? 'No description' }}</td>
                    <td>${{ number_format($expense->amount ?? 0, 2) }}</td>
                    <td>{{ $expense->expense_date ? $expense->expense_date->format('M j, Y') : 'Not provided' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This document was generated by MySuperTax Client Management System</p>
        <p>© {{ date('Y') }} MySuperTax. All rights reserved.</p>
    </div>
</body>
</html>