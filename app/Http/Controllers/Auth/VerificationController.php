<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class VerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function show(Request $request): Response
    {
        if ($request->user()->hasVerifiedEmail()) {
            $user = $request->user();
            
            if ($user->isAdmin() || $user->isTaxProfessional()) {
                return redirect()->intended(route('admin.dashboard'));
            } else {
                return redirect()->intended(route('client.dashboard'));
            }
        }
        
        return Inertia::render('Auth/VerifyEmail');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            $user = $request->user();
            
            if ($user->isAdmin() || $user->isTaxProfessional()) {
                return redirect()->intended(route('admin.dashboard'));
            } else {
                return redirect()->intended(route('client.dashboard'));
            }
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        $user = $request->user();
        
        // Redirect based on user role
        if ($user->isAdmin() || $user->isTaxProfessional()) {
            return redirect()->intended(route('admin.dashboard'))->with('verified', true);
        } else {
            return redirect()->intended(route('client.dashboard'))->with('verified', true);
        }
    }

    /**
     * Send a new email verification notification.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            $user = $request->user();
            
            if ($user->isAdmin() || $user->isTaxProfessional()) {
                return redirect()->intended(route('admin.dashboard'));
            } else {
                return redirect()->intended(route('client.dashboard'));
            }
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}