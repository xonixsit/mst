@extends('emails.layout')

@section('title', 'Review Request from ' . $clientName)

@section('content')
<div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h2 style="color: #1f2937; margin: 0 0 10px 0; font-size: 20px;">
        📋 Review Request Received
    </h2>
    <p style="color: #6b7280; margin: 0; font-size: 14px;">
        A client has requested a review of their tax information
    </p>
</div>

<div style="margin-bottom: 24px;">
    <h3 style="color: #374151; margin: 0 0 12px 0; font-size: 16px;">Client Information</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; color: #6b7280; font-weight: 500; width: 120px;">Name:</td>
            <td style="padding: 8px 0; color: #1f2937;">{{ $clientName }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Email:</td>
            <td style="padding: 8px 0; color: #1f2937;">{{ $clientEmail }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Priority:</td>
            <td style="padding: 8px 0;">
                <span style="
                    display: inline-block;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: 500;
                    text-transform: uppercase;
                    {{ $priority === 'high' ? 'background-color: #fee2e2; color: #dc2626;' : 
                       ($priority === 'medium' ? 'background-color: #fef3c7; color: #d97706;' : 
                        'background-color: #e0f2fe; color: #0369a1;') }}
                ">
                    {{ ucfirst($priority) }}
                </span>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Requested:</td>
            <td style="padding: 8px 0; color: #1f2937;">{{ $requestedAt }}</td>
        </tr>
    </table>
</div>

@if(!empty($sections))
<div style="margin-bottom: 24px;">
    <h3 style="color: #374151; margin: 0 0 12px 0; font-size: 16px;">Sections to Review</h3>
    <div style="background-color: #f9fafb; padding: 16px; border-radius: 6px; border-left: 4px solid #3b82f6;">
        @foreach($sections as $section)
            <div style="margin-bottom: 8px; color: #374151;">
                <span style="color: #3b82f6; margin-right: 8px;">•</span>
                {{ $sectionNames[$section] ?? ucfirst($section) }}
            </div>
        @endforeach
    </div>
</div>
@endif

@if(!empty($message))
<div style="margin-bottom: 24px;">
    <h3 style="color: #374151; margin: 0 0 12px 0; font-size: 16px;">Client Message</h3>
    <div style="background-color: #f9fafb; padding: 16px; border-radius: 6px; border-left: 4px solid #10b981;">
        <p style="color: #374151; margin: 0; line-height: 1.6;">{{ $message }}</p>
    </div>
</div>
@endif

<div style="text-align: center; margin: 32px 0;">
    <a href="{{ $actionUrl }}" 
       style="
           display: inline-block;
           background-color: #3b82f6;
           color: white;
           padding: 12px 24px;
           text-decoration: none;
           border-radius: 6px;
           font-weight: 500;
           font-size: 14px;
       ">
        Review Client Information
    </a>
</div>

<div style="background-color: #f3f4f6; padding: 16px; border-radius: 6px; margin-top: 24px;">
    <p style="color: #6b7280; margin: 0; font-size: 14px; text-align: center;">
        <strong>Next Steps:</strong><br>
        1. Review the client's information sections<br>
        2. Contact the client if clarification is needed<br>
        3. Update the client's status once review is complete
    </p>
</div>
@endsection