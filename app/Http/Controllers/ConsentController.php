<?php

namespace App\Http\Controllers;

use App\Models\UserConsent;
use App\Models\ConsentLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConsentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'consents' => 'required|array',
            'consents.*.type' => 'required|string|in:terms,privacy,marketing,analytics,cookies',
            'consents.*.given' => 'required|boolean',
            'source' => 'nullable|string|in:banner,settings,registration',
        ]);

        $userId = auth()->id();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $source = $validated['source'] ?? 'banner';

        $consentData = [];
        foreach ($validated['consents'] as $consent) {
            UserConsent::recordConsent(
                $userId,
                $consent['type'],
                $consent['given'],
                $ipAddress,
                $userAgent
            );
            $consentData[$consent['type']] = $consent['given'];
        }

        ConsentLog::log(
            $userId,
            'accepted',
            $consentData,
            $ipAddress,
            $userAgent,
            $source
        );

        return response()->json([
            'success' => true,
            'message' => 'Consent preferences saved successfully',
        ]);
    }

    public function getConsents(Request $request): JsonResponse
    {
        $userId = auth()->id();

        $consents = UserConsent::where('user_id', $userId)
            ->pluck('given', 'consent_type')
            ->toArray();

        return response()->json([
            'consents' => $consents,
            'has_consented' => !empty($consents),
        ]);
    }

    public function withdraw(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'consent_type' => 'required|string|in:terms,privacy,marketing,analytics,cookies',
        ]);

        $userId = auth()->id();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        UserConsent::where('user_id', $userId)
            ->where('consent_type', $validated['consent_type'])
            ->update(['given' => false]);

        ConsentLog::log(
            $userId,
            'withdrawn',
            [$validated['consent_type'] => false],
            $ipAddress,
            $userAgent,
            'settings'
        );

        return response()->json([
            'success' => true,
            'message' => 'Consent withdrawn successfully',
        ]);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'consents' => 'required|array',
            'consents.*.type' => 'required|string|in:terms,privacy,marketing,analytics,cookies',
            'consents.*.given' => 'required|boolean',
        ]);

        $userId = auth()->id();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        $consentData = [];
        foreach ($validated['consents'] as $consent) {
            UserConsent::recordConsent(
                $userId,
                $consent['type'],
                $consent['given'],
                $ipAddress,
                $userAgent
            );
            $consentData[$consent['type']] = $consent['given'];
        }

        ConsentLog::log(
            $userId,
            'updated',
            $consentData,
            $ipAddress,
            $userAgent,
            'settings'
        );

        return response()->json([
            'success' => true,
            'message' => 'Preferences updated successfully',
        ]);
    }

    public function getConsentHistory(Request $request): JsonResponse
    {
        $userId = auth()->id();

        $history = ConsentLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'history' => $history,
        ]);
    }
}
