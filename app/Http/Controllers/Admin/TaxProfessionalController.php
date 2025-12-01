<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TaxProfessionalController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || auth()->user()->role !== 'admin') {
                abort(403, 'Access denied. Admin privileges required.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = User::where('role', 'tax_professional');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->status === 'inactive') {
                $query->whereNotNull('deleted_at');
            }
        }

        $taxProfessionals = $query->withTrashed()
                                 ->withCount(['clients'])
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(25)
                                 ->withQueryString();

        // Debug: Log the count of tax professionals
        \Log::info('Tax Professionals Count:', ['count' => $taxProfessionals->total()]);

        $stats = [
            'total_professionals' => User::where('role', 'tax_professional')->count(),
            'active_professionals' => User::where('role', 'tax_professional')->whereNull('deleted_at')->count(),
            'inactive_professionals' => User::where('role', 'tax_professional')->whereNotNull('deleted_at')->count(),
            'total_clients_managed' => User::where('role', 'tax_professional')
                                          ->withCount(['clients' => function($query) {
                                              // Don't filter by deleted_at since clients table doesn't have it
                                          }])
                                          ->get()
                                          ->sum('clients_count'),
        ];

        return Inertia::render('Admin/TaxProfessionals/Index', [
            'taxProfessionals' => $taxProfessionals,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/TaxProfessionals/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:10',
            'license_number' => 'nullable|string|max:100',
            'specializations' => 'nullable|array',
            'bio' => 'nullable|string|max:1000',
        ]);

        $validated['role'] = 'tax_professional';
        $validated['password'] = Hash::make($validated['password']);
        $validated['specializations'] = json_encode($validated['specializations'] ?? []);

        User::create($validated);

        return redirect()->route('admin.tax-professionals.index')
                       ->with('success', 'Tax professional created successfully!');
    }

    public function show(User $taxProfessional)
    {
        if ($taxProfessional->role !== 'tax_professional') {
            abort(404);
        }

        $taxProfessional->load(['clients.invoices']);
        
        // Calculate stats for this professional
        $stats = [
            'total_clients' => $taxProfessional->clients()->count(),
            'active_clients' => $taxProfessional->clients()->count(), // All clients are considered active since no soft deletes
            'total_invoices' => $taxProfessional->clients()->withCount('invoices')->get()->sum('invoices_count'),
            'total_revenue' => $taxProfessional->clients()
                                             ->with('invoices')
                                             ->get()
                                             ->flatMap->invoices
                                             ->where('status', 'paid')
                                             ->sum('total_amount'),
        ];

        return Inertia::render('Admin/TaxProfessionals/Show', [
            'taxProfessional' => $taxProfessional,
            'stats' => $stats,
        ]);
    }

    public function edit(User $taxProfessional)
    {
        if ($taxProfessional->role !== 'tax_professional') {
            abort(404);
        }

        // Debug: Log the tax professional data
        \Log::info('Tax Professional Edit Data:', $taxProfessional->toArray());

        return Inertia::render('Admin/TaxProfessionals/Edit', [
            'taxProfessional' => $taxProfessional,
        ]);
    }

    public function update(Request $request, User $taxProfessional)
    {
        \Log::info('Update method called for tax professional ID: ' . $taxProfessional->id);
        \Log::info('Request data:', $request->all());
        
        if ($taxProfessional->role !== 'tax_professional') {
            abort(404);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($taxProfessional->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:10',
            'license_number' => 'nullable|string|max:100',
            'specializations' => 'nullable|array',
            'bio' => 'nullable|string|max:1000',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['specializations'] = json_encode($validated['specializations'] ?? []);

        $taxProfessional->update($validated);

        return redirect()->route('admin.tax-professionals.show', $taxProfessional)
                       ->with('success', 'Tax professional updated successfully!');
    }

    public function destroy(User $taxProfessional)
    {
        if ($taxProfessional->role !== 'tax_professional') {
            abort(404);
        }

        $taxProfessional->delete();

        return redirect()->route('admin.tax-professionals.index')
                       ->with('success', 'Tax professional deactivated successfully!');
    }

    public function restore(User $taxProfessional)
    {
        if ($taxProfessional->role !== 'tax_professional') {
            abort(404);
        }

        $taxProfessional->restore();

        return redirect()->route('admin.tax-professionals.index')
                       ->with('success', 'Tax professional reactivated successfully!');
    }
}