<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TwoFactorAuthController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Show 2FA verification page during login
     */
    public function verify()
    {
        if (!session('2fa_pending')) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/TwoFactorVerify');
    }

    /**
     * Verify 2FA code during login
     */
    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ]);

        if (!session('2fa_pending') || !session('2fa_user_id')) {
            return back()->withErrors(['code' => 'Session expired']);
        }

        $user = \App\Models\User::find(session('2fa_user_id'));

        if (!$user) {
            return back()->withErrors(['code' => 'User not found']);
        }

        if (!$this->twoFactorService->verifyCode($user, $validated['code'])) {
            return back()->withErrors(['code' => 'Invalid verification code']);
        }

        auth()->login($user);
        session()->forget(['2fa_pending', '2fa_user_id']);
        
        // Redirect based on user role
        if ($user->isAdmin() || $user->isTaxProfessional()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('client.dashboard');
        }
    }

    /**
     * Resend 2FA code
     */
    public function resendCode(Request $request)
    {
        if (!session('2fa_pending') || !session('2fa_user_id')) {
            return back()->withErrors(['code' => 'Session expired']);
        }

        $user = \App\Models\User::find(session('2fa_user_id'));

        if (!$user) {
            return back()->withErrors(['code' => 'User not found']);
        }

        $this->twoFactorService->sendCode($user);

        return back()->with('success', 'Verification code sent to your email');
    }

    /**
     * Show 2FA setup page
     */
    public function setup()
    {
        $user = auth()->user();
        
        if ($user->two_factor_enabled) {
            return redirect()->route('admin.profile')->with('info', '2FA is already enabled');
        }

        return Inertia::render('Auth/TwoFactorSetup', [
            'qrCode' => '', // Not needed for email OTP
            'secret' => '', // Not needed for email OTP
        ]);
    }

    /**
     * Confirm 2FA setup (enable email OTP)
     */
    public function confirm(Request $request)
    {
        $user = auth()->user();

        // Simply enable 2FA for email OTP
        $this->twoFactorService->enableTwoFactor($user);

        return redirect()->route('admin.profile')->with('success', '2FA has been enabled');
    }

    /**
     * Get backup codes
     */
    public function backupCodes()
    {
        $user = auth()->user();

        if (!$user->two_factor_enabled) {
            return response()->json(['error' => 'Two-factor authentication is not enabled'], 403);
        }

        $codes = $this->twoFactorService->getBackupCodes($user);

        return response()->json(['codes' => $codes]);
    }

    /**
     * Regenerate backup codes
     */
    public function regenerateBackupCodes(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();

        if (!$user->two_factor_enabled) {
            return response()->json(['error' => 'Two-factor authentication is not enabled'], 403);
        }

        $codes = $this->twoFactorService->regenerateBackupCodes($user);

        return response()->json(['codes' => $codes]);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|current_password',
        ]);

        $this->twoFactorService->disableTwoFactor(auth()->user());

        return back()->with('success', '2FA has been disabled');
    }
}
