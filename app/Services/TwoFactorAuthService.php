<?php

namespace App\Services;

use App\Models\User;
use App\Models\TwoFactorCode;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TwoFactorAuthService
{
    /**
     * Generate a secret for 2FA (not used for email OTP)
     */
    public function generateSecret(): string
    {
        return Str::random(32);
    }

    /**
     * Generate QR code for 2FA setup (not used for email OTP)
     */
    public function generateQrCode(string $email, string $secret): string
    {
        return ''; // Not needed for email OTP
    }

    /**
     * Verify 2FA code (email OTP)
     */
    public function verifyCode(User $user, string $code, ?string $secret = null): bool
    {
        // For email OTP, check against stored codes
        $twoFactorCode = $user->twoFactorCodes()
            ->where('code', $code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($twoFactorCode) {
            $twoFactorCode->update(['used' => true]);
            return true;
        }

        return false;
    }

    /**
     * Enable 2FA for user (email-based)
     */
    public function enableTwoFactor(User $user, string $secret = null): array
    {
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => null, // Not needed for email OTP
            'two_factor_backup_codes' => null, // Not needed for email OTP
        ]);

        return []; // No backup codes for email OTP
    }

    /**
     * Disable 2FA for user
     */
    public function disableTwoFactor(User $user): void
    {
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_backup_codes' => null,
        ]);

        // Delete any unused codes
        $user->twoFactorCodes()->delete();
    }

    /**
     * Generate backup codes (not used for email OTP)
     */
    public function generateBackupCodes(): array
    {
        return []; // Not needed for email OTP
    }

    /**
     * Get backup codes for user (not used for email OTP)
     */
    public function getBackupCodes(User $user): array
    {
        return []; // Not needed for email OTP
    }

    /**
     * Regenerate backup codes (not used for email OTP)
     */
    public function regenerateBackupCodes(User $user): array
    {
        return []; // Not needed for email OTP
    }

    /**
     * Send 2FA code via email
     */
    public function sendCode(User $user): TwoFactorCode
    {
        // Delete old unused codes
        $user->twoFactorCodes()
            ->where('used', false)
            ->where('expires_at', '<', now())
            ->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $twoFactorCode = $user->twoFactorCodes()->create([
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send email with code
        Mail::to($user->email)->send(new TwoFactorCodeMail($user, $code));

        return $twoFactorCode;
    }
}
