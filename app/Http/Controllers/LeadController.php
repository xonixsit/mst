<?php

namespace App\Http\Controllers;

use App\Mail\LeadNotificationMail;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:leads,email',
            'phone' => 'required|string|max:20',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);

        $lead = Lead::create($validated);

        try {
            Mail::to(config('mail.from.address'))->send(new LeadNotificationMail($lead));
        } catch (\Exception $e) {
            \Log::error('Lead notification email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your inquiry. We will contact you soon.',
        ]);
    }
}
