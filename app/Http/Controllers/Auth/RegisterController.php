<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ClientWelcomeNotification;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function __construct(
        private AdminNotificationService $adminNotificationService
    ) {}

    /**
     * Show the registration form.
     */
    public function showRegistrationForm(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:client,tax_professional',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        Auth::login($user);

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        // Send welcome notification for clients
        if ($user->isClient()) {
            $user->notify(new ClientWelcomeNotification($user));
            
            // Notify admins about new client registration
            $this->adminNotificationService->notifyClientRegistered($user);
        }

        // Redirect to email verification notice
        if ($user->isTaxProfessional()) {
            return redirect()->route('admin.verification.notice');
        } else {
            return redirect()->route('client.verification.notice');
        }
    }
}