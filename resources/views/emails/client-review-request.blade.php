@extends('emails.layout')

@section('content')
<h1>Review Request from {{ $client->full_name }}</h1>

<p>You have received a new review request from <strong>{{ $client->full_name }}</strong>.</p>

<div style="margin: 20px 0;">
    <p><strong>Sections to review:</strong> {{ empty($sections) ? 'All sections' : implode(', ', $sections) }}</p>
    <p><strong>Priority:</strong> {{ ucfirst($priority) }}</p>
    
    @if($message)
    <p><strong>Client message:</strong></p>
    <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 10px 0;">
        {{ $message }}
    </div>
    @endif
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/admin/clients/' . $client->id) }}" 
       style="background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;">
        Review Client Information
    </a>
</div>

<p>Please review the client information and provide feedback as needed.</p>
@endsection