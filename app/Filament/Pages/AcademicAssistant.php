<?php

namespace App\Filament\Pages;

use App\Models\AcademicAssistantMessage;
use App\Models\AcademicAssistantSession;
use App\Services\GeminiService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class AcademicAssistant extends Page
{
    protected string $view = 'filament.pages.academic-assistant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'AI Academic Assistant';

    protected static ?string $title = 'AI Academic Assistant';

    protected static ?string $slug = 'academic-assistant';

    protected static ?int $navigationSort = 100;

    public static function getNavigationGroup(): ?string
    {
        return 'Bantuan Akademik';
    }

    // =========================================================================
    // LIVEWIRE PROPERTIES
    // =========================================================================

    public ?int $currentSessionId = null;

    public string $messageText = '';

    public bool $isLoading = false;

    public string $errorMessage = '';

    // =========================================================================
    // COMPUTED DATA
    // =========================================================================

    public function getSessions()
    {
        return AcademicAssistantSession::where('user_id', Auth::id())
            ->orderByDesc('updated_at')
            ->get();
    }

    public function getMessages()
    {
        if (! $this->currentSessionId) {
            return collect([]);
        }

        return AcademicAssistantMessage::where('session_id', $this->currentSessionId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    // =========================================================================
    // ACTIONS
    // =========================================================================

    public function startNewSession(): void
    {
        $session = AcademicAssistantSession::create([
            'user_id' => Auth::id(),
            'title' => 'Sesi Baru',
        ]);

        $this->currentSessionId = $session->id;
        $this->messageText = '';
        $this->errorMessage = '';
    }

    public function selectSession(int $sessionId): void
    {
        $session = AcademicAssistantSession::where('user_id', Auth::id())
            ->where('id', $sessionId)
            ->first();

        if ($session) {
            $this->currentSessionId = $session->id;
            $this->errorMessage = '';
        }
    }

    public function deleteSession(int $sessionId): void
    {
        $session = AcademicAssistantSession::where('user_id', Auth::id())
            ->where('id', $sessionId)
            ->first();

        if ($session) {
            $session->delete();

            if ($this->currentSessionId === $sessionId) {
                $this->currentSessionId = null;
            }
        }
    }

    public function sendMessage(): void
    {
        $text = trim($this->messageText);
        if (empty($text)) {
            return;
        }

        $this->errorMessage = '';

        // Auto-create session if none selected
        if (! $this->currentSessionId) {
            $this->startNewSession();
        }

        // Save user message
        AcademicAssistantMessage::create([
            'session_id' => $this->currentSessionId,
            'role' => 'user',
            'content' => $text,
        ]);

        // Auto-generate session title from first message
        $session = AcademicAssistantSession::find($this->currentSessionId);
        if ($session && $session->title === 'Sesi Baru') {
            $session->update([
                'title' => mb_substr($text, 0, 50).(mb_strlen($text) > 50 ? '...' : ''),
            ]);
        }

        $this->messageText = '';
        $this->isLoading = true;

        try {
            // Build conversation history for multi-turn context
            $history = AcademicAssistantMessage::where('session_id', $this->currentSessionId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(fn ($msg) => [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ])
                ->toArray();

            $gemini = new GeminiService;
            $reply = $gemini->generateResponse($history);

            // Save AI response
            AcademicAssistantMessage::create([
                'session_id' => $this->currentSessionId,
                'role' => 'model',
                'content' => $reply,
            ]);

            // Touch session to update its timestamp
            $session->touch();

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->isLoading = false;
        }
    }

    public function useSuggestion(string $suggestion): void
    {
        $this->messageText = $suggestion;
        $this->sendMessage();
    }
}
