<?php

namespace App\Http\Controllers;

use App\Models\AcademicAssistantSession;
use App\Models\AcademicAssistantMessage;
use App\Models\AcademicAssistantUsage;
use App\Models\AppSetting;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiChatController extends Controller
{
    /**
     * Legacy / Guest API generate endpoint (uses local storage history).
     */
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
                'quota' => max(0, $maxQuota - $newUsage),
                'max_quota' => $maxQuota,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'quota' => max(0, $maxQuota - $currentUsage),
                'max_quota' => $maxQuota,
            ], 500);
        }
    }

    /**
     * Get all sessions for the current logged-in user + quota info.
     */
    public function getSessions(Request $request)
    {
        $userId = auth()->id();
        $sessions = AcademicAssistantSession::where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get();

        $maxQuota = (int) AppSetting::get('ai_daily_quota', 20);
        $currentUsage = AcademicAssistantUsage::todayCountForUser($userId);

        return response()->json([
            'sessions' => $sessions,
            'quota' => max(0, $maxQuota - $currentUsage),
            'max_quota' => $maxQuota,
        ]);
    }

    /**
     * Create a new chat session.
     */
    public function createSession(Request $request)
    {
        $session = AcademicAssistantSession::create([
            'user_id' => auth()->id(),
            'title' => 'Sesi Baru',
        ]);

        return response()->json($session, 201);
    }

    /**
     * Get all messages for a specific session.
     */
    public function getSessionMessages($id)
    {
        try {
            $session = AcademicAssistantSession::where('user_id', auth()->id())
                ->findOrFail($id);
            return response()->json($session->messages);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi percakapan tidak ditemukan.',
                'session_not_found' => true,
            ], 404);
        }
    }

    /**
     * Delete a chat session.
     */
    public function deleteSession($id)
    {
        try {
            $session = AcademicAssistantSession::where('user_id', auth()->id())
                ->findOrFail($id);
            $session->delete();
            return response()->json(['success' => true]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi percakapan tidak ditemukan.',
                'session_not_found' => true,
            ], 404);
        }
    }

    /**
     * Send a message within a session.
     */
    public function sendMessage(Request $request, $sessionId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        try {
            $session = AcademicAssistantSession::where('user_id', auth()->id())
                ->findOrFail($sessionId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi percakapan tidak ditemukan atau telah dihapus.',
                'session_not_found' => true,
            ], 404);
        }

        $maxQuota = (int) AppSetting::get('ai_daily_quota', 20);
        $userId = auth()->id();
        $currentUsage = AcademicAssistantUsage::todayCountForUser($userId);

        if ($currentUsage >= $maxQuota) {
            return response()->json([
                'success' => false,
                'message' => "Kuota bertanya Anda ({$maxQuota}/{$maxQuota}) telah habis. Silakan coba lagi besok.",
                'quota' => 0,
                'max_quota' => $maxQuota,
            ], 429);
        }

        // Format history from database
        $messages = $session->messages()->get();
        $history = [];
        foreach ($messages as $msg) {
            $history[] = [
                'role' => $msg->role === 'model' ? 'model' : 'user',
                'content' => $msg->content,
            ];
        }
        $history[] = [
            'role' => 'user',
            'content' => $request->content,
        ];

        try {
            $gemini = new GeminiService;
            $reply = $gemini->generateResponse($history);

            // DB Transaction to ensure data integrity
            DB::transaction(function () use ($session, $request, $reply, $userId) {
                // Save user message
                AcademicAssistantMessage::create([
                    'session_id' => $session->id,
                    'role' => 'user',
                    'content' => $request->content,
                ]);

                // Save AI response
                AcademicAssistantMessage::create([
                    'session_id' => $session->id,
                    'role' => 'model',
                    'content' => $reply,
                ]);

                // Auto update title if it's the default "Sesi Baru"
                if ($session->title === 'Sesi Baru') {
                    $session->title = mb_substr($request->content, 0, 50) . (mb_strlen($request->content) > 50 ? '...' : '');
                }

                $session->touch(); // trigger updated_at
                $session->save();

                AcademicAssistantUsage::incrementForUser($userId, today()->toDateString());
            });

            $newUsage = $currentUsage + 1;

            return response()->json([
                'success' => true,
                'reply' => $reply,
                'quota' => max(0, $maxQuota - $newUsage),
                'max_quota' => $maxQuota,
                'session_title' => $session->title,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'quota' => max(0, $maxQuota - $currentUsage),
                'max_quota' => $maxQuota,
            ], 500);
        }
    }
}
