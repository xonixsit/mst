<?php

namespace App\Services;

use App\Models\User;
use App\Models\TwoFactorCode;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthService
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Generate a secret for 2FA
     */
    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Generate QR code for 2FA setup
     */
    public function generateQrCode(string $email, string $secret): string
    {
        return $this->google2fa->getQRCodeInline(
            config('app.name'),
            $email,
            $secret
        );
    }

    /**
     * Verify 2FA code
     */
    public function verifyCode(User $user, string $code, ?string $secret = null): bool
    {
        $secret = $secret ?? $user->two_factor_secret;

        if (!$secret) {
            return false;
        }

        return $this->google2fa->verifyKey($secret, $code);
    }

    /**
     * Enable 2FA for user
     */
    public function enableTwoFactor(User $user, string $secret): array
    {
        $backupCodes = $this->generateBackupCodes();

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => $secret,
            'two_factor_backup_codes' => json_encode($backupCodes),
        ]);

        return $backupCodes;
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
    }

    /**
     * Generate backup codes
     */
    public function generateBackupCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = Str::random(8);
        }
        return $codes;
    }

    /**
     * Get backup codes for user
     */
    public function getBackupCodes(User $user): array
    {
        if (!$user->two_factor_backup_codes) {
            return [];
        }

        return json_decode($user->two_factor_backup_codes, true);
    }

    /**
     * Regenerate backup codes
     */
    public function regenerateBackupCodes(User $user): array
    {
        $backupCodes = $this->generateBackupCodes();

        $user->update([
            'two_factor_backup_codes' => json_encode($backupCodes),
        ]);

        return $backupCodes;
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
