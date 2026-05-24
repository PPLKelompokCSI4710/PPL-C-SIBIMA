<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AiChatController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'history' => 'required|array',
            'history.*.role' => 'required|in:user,model',
            'history.*.content' => 'required|string',
        ]);

        $maxQuota = 20;
        $identifier = auth()->check() ? 'user_'.auth()->id() : 'ip_'.$request->ip();
        $cacheKey = "ai_quota_{$identifier}";

        $currentUsage = Cache::get($cacheKey, 0);

        if ($currentUsage >= $maxQuota) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota bertanya Anda (20/20) telah habis. Silakan coba lagi nanti.',
                'quota' => 0,
                'max_quota' => $maxQuota,
            ], 429);
        }

        try {
            $gemini = new GeminiService;
            $reply = $gemini->generateResponse($request->history);

            // Increment usage
            Cache::put($cacheKey, $currentUsage + 1, now()->addHours(12));

            return response()->json([
                'success' => true,
                'reply' => $reply,
                'quota' => $maxQuota - ($currentUsage + 1),
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
