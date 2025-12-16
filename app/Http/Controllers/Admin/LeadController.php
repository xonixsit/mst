<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::orderBy('created_at', 'desc')->get();
        $taxProfessionals = \App\Models\User::where('role', 'tax_professional')->get(['id', 'first_name', 'last_name', 'email']);
        
        return inertia('Admin/Leads', [
            'leads' => $leads,
            'taxProfessionals' => $taxProfessionals,
        ]);
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,converted,rejected',
        ]);

        $lead->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Lead status updated successfully',
            'lead' => $lead,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:new,contacted,converted,rejected',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $lead = Lead::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'lead' => $lead,
        ]);
    }

    public function assignLead(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $lead->update(['assigned_to' => $validated['assigned_to']]);

        return response()->json([
            'success' => true,
            'message' => 'Lead assigned successfully',
            'lead' => $lead,
        ]);
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully',
        ]);
    }
}
