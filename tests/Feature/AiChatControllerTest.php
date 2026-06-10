<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiChatControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.key' => 'dummy-key']);
    }

    public function test_api_chat_returns_successful_response_and_decrements_quota(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'Ini jawaban AI']]]],
                ],
            ], 200),
        ]);

        $history = [
            ['role' => 'user', 'content' => 'Halo'],
        ];

        $response = $this->postJson('/api/ai-chat', [
            'history' => $history,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'reply' => 'Ini jawaban AI',
                'quota' => 19,
                'max_quota' => 20,
            ]);
    }

    public function test_api_chat_validates_history_array_format(): void
    {
        $response = $this->postJson('/api/ai-chat', [
            'history' => 'Bukan array',
        ]);

        $response->assertStatus(422);

        $response2 = $this->postJson('/api/ai-chat', [
            'history' => [
                ['role' => 'invalid-role', 'content' => 'Halo'],
            ],
        ]);

        $response2->assertStatus(422);
    }

    public function test_api_chat_enforces_quota_limit(): void
    {
        // Simulate usage at limit
        Cache::put('ai_quota_ip_127.0.0.1', 20, now()->addHours(12));

        $response = $this->postJson('/api/ai-chat', [
            'history' => [
                ['role' => 'user', 'content' => 'Halo'],
            ],
        ]);

        $response->assertStatus(429)
            ->assertJson([
                'success' => false,
                'message' => 'Kuota bertanya Anda (20/20) telah habis. Silakan coba lagi besok.',
                'quota' => 0,
            ]);
    }
}
