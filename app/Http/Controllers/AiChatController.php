<?php

namespace App\Http\Controllers;

use App\Models\AcademicAssistantUsage;
use App\Models\AppSetting;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'history' => 'required|array',
            'history.*.role' => 'required|in:user,model',
            'history.*.content' => 'required|string',
        ]);

        // Fetch dynamic quota from AppSetting, fallback to 20
        $maxQuota = (int) AppSetting::get('ai_daily_quota', 20);

        // Determine the user identifier
        $userId = auth()->check() ? auth()->id() : null;

        // Get today's usage from database (for authenticated users) or Cache (for guests)
        if ($userId) {
            $currentUsage = AcademicAssistantUsage::todayCountForUser($userId);
        } else {
            $identifier = 'ip_' . $request->ip();
            $cacheKey = "ai_quota_{$identifier}";
            $currentUsage = \Illuminate\Support\Facades\Cache::get($cacheKey, 0);
        }

        if ($currentUsage >= $maxQuota) {
            return response()->json([
                'success' => false,
                'message' => "Kuota bertanya Anda ({$maxQuota}/{$maxQuota}) telah habis. Silakan coba lagi besok.",
                'quota' => 0,
                'max_quota' => $maxQuota,
            ], 429);
        }

        try {
            $gemini = new GeminiService;
            $reply = $gemini->generateResponse($request->history);

            // Increment usage in DB (authenticated) or Cache (guest)
            if ($userId) {
                AcademicAssistantUsage::incrementForUser($userId, today()->toDateString());
                $newUsage = $currentUsage + 1;
            } else {
                \Illuminate\Support\Facades\Cache::put($cacheKey, $currentUsage + 1, now()->addHours(24));
                $newUsage = $currentUsage + 1;
            }

            return response()->json([
                'success' => true,
                'reply' => $reply,
                'quota' => $maxQuota - $newUsage,
                'max_quota' => $maxQuota,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'quota' => $maxQuota - $currentUsage,
                'max_quota' => $maxQuota,
            ], 500);
        }
    }
}
