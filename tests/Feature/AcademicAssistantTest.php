<?php

namespace Tests\Feature;

use App\Models\AcademicAssistantMessage;
use App\Models\AcademicAssistantSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_user_can_create_a_new_session(): void
    {
        $this->actingAs($this->user);

        $session = AcademicAssistantSession::create([
            'user_id' => $this->user->id,
            'title' => 'Sesi Baru',
        ]);

        $this->assertDatabaseHas('academic_assistant_sessions', [
            'id' => $session->id,
            'user_id' => $this->user->id,
            'title' => 'Sesi Baru',
        ]);
    }

    public function test_user_can_store_messages_in_session(): void
    {
        $this->actingAs($this->user);

        $session = AcademicAssistantSession::create([
            'user_id' => $this->user->id,
            'title' => 'Test Session',
        ]);

        $userMessage = AcademicAssistantMessage::create([
            'session_id' => $session->id,
            'role' => 'user',
            'content' => 'Bagaimana cara menentukan judul skripsi?',
        ]);

        $aiMessage = AcademicAssistantMessage::create([
            'session_id' => $session->id,
            'role' => 'model',
            'content' => 'Berikut adalah panduan menentukan judul skripsi...',
        ]);

        $this->assertDatabaseHas('academic_assistant_messages', [
            'session_id' => $session->id,
            'role' => 'user',
            'content' => 'Bagaimana cara menentukan judul skripsi?',
        ]);

        $this->assertDatabaseHas('academic_assistant_messages', [
            'session_id' => $session->id,
            'role' => 'model',
        ]);

        $this->assertEquals(2, $session->messages()->count());
    }

    public function test_session_messages_are_ordered_by_created_at(): void
    {
        $session = AcademicAssistantSession::create([
            'user_id' => $this->user->id,
            'title' => 'Ordered Test',
        ]);

        $msg1 = AcademicAssistantMessage::create([
            'session_id' => $session->id,
            'role' => 'user',
            'content' => 'First message',
            'created_at' => now()->subMinutes(2),
        ]);

        $msg2 = AcademicAssistantMessage::create([
            'session_id' => $session->id,
            'role' => 'model',
            'content' => 'Second message',
            'created_at' => now()->subMinute(),
        ]);

        $msg3 = AcademicAssistantMessage::create([
            'session_id' => $session->id,
            'role' => 'user',
            'content' => 'Third message',
            'created_at' => now(),
        ]);

        $messages = $session->messages()->get();

        $this->assertEquals('First message', $messages[0]->content);
        $this->assertEquals('Second message', $messages[1]->content);
        $this->assertEquals('Third message', $messages[2]->content);
    }

    public function test_deleting_session_cascades_to_messages(): void
    {
        $session = AcademicAssistantSession::create([
            'user_id' => $this->user->id,
            'title' => 'Cascade Delete Test',
        ]);

        AcademicAssistantMessage::create([
            'session_id' => $session->id,
            'role' => 'user',
            'content' => 'Test message 1',
        ]);

        AcademicAssistantMessage::create([
            'session_id' => $session->id,
            'role' => 'model',
            'content' => 'Test reply 1',
        ]);

        $sessionId = $session->id;
        $session->delete();

        $this->assertDatabaseMissing('academic_assistant_sessions', ['id' => $sessionId]);
        $this->assertDatabaseMissing('academic_assistant_messages', ['session_id' => $sessionId]);
    }

    public function test_session_title_auto_updates_from_first_message(): void
    {
        $session = AcademicAssistantSession::create([
            'user_id' => $this->user->id,
            'title' => 'Sesi Baru',
        ]);

        $firstMessage = 'Bagaimana cara membuat proposal skripsi yang baik dan benar?';

        // Simulate auto-title logic from the Filament page
        if ($session->title === 'Sesi Baru') {
            $session->update([
                'title' => mb_substr($firstMessage, 0, 50).(mb_strlen($firstMessage) > 50 ? '...' : ''),
            ]);
        }

        $session->refresh();
        $this->assertEquals('Bagaimana cara membuat proposal skripsi yang baik ...', $session->title);
    }

    public function test_user_can_get_sessions_via_api(): void
    {
        $this->actingAs($this->user);

        AcademicAssistantSession::create([
            'user_id' => $this->user->id,
            'title' => 'Sesi A',
        ]);

        $response = $this->getJson(route('api.ai-chat.sessions'));

        $response->assertStatus(200)
            ->assertJsonStructure(['sessions', 'quota', 'max_quota'])
            ->assertJsonFragment(['title' => 'Sesi A']);
    }

    public function test_user_can_create_session_via_api(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('api.ai-chat.create-session'));

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Sesi Baru']);

        $this->assertDatabaseHas('academic_assistant_sessions', [
            'user_id' => $this->user->id,
            'title' => 'Sesi Baru',
        ]);
    }

    public function test_user_can_get_session_messages_via_api(): void
    {
        $this->actingAs($this->user);

        $session = AcademicAssistantSession::create([
            'user_id' => $this->user->id,
            'title' => 'Sesi Test',
        ]);

        AcademicAssistantMessage::create([
            'session_id' => $session->id,
            'role' => 'user',
            'content' => 'Halo AI',
        ]);

        $response = $this->getJson(route('api.ai-chat.messages', ['id' => $session->id]));

        $response->assertStatus(200)
            ->assertJsonFragment(['content' => 'Halo AI']);
    }

    public function test_user_can_delete_session_via_api(): void
    {
        $this->actingAs($this->user);

        $session = AcademicAssistantSession::create([
            'user_id' => $this->user->id,
            'title' => 'Delete Me',
        ]);

        $response = $this->deleteJson(route('api.ai-chat.delete-session', ['id' => $session->id]));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('academic_assistant_sessions', [
            'id' => $session->id,
        ]);
    }

    public function test_user_can_send_message_in_session_and_get_reply(): void
    {
        $this->actingAs($this->user);
        \Illuminate\Support\Facades\Http::fake([
            'generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Ini adalah tanggapan skripsi Anda.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $session = AcademicAssistantSession::create([
            'user_id' => $this->user->id,
            'title' => 'Sesi Baru',
        ]);

        $response = $this->postJson(route('api.ai-chat.send-message', ['id' => $session->id]), [
            'content' => 'Bagaimana cara menentukan rumusan masalah?',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'reply' => 'Ini adalah tanggapan skripsi Anda.',
                'session_title' => 'Bagaimana cara menentukan rumusan masalah?',
            ]);

        $this->assertDatabaseHas('academic_assistant_messages', [
            'session_id' => $session->id,
            'role' => 'user',
            'content' => 'Bagaimana cara menentukan rumusan masalah?',
        ]);

        $this->assertDatabaseHas('academic_assistant_messages', [
            'session_id' => $session->id,
            'role' => 'model',
            'content' => 'Ini adalah tanggapan skripsi Anda.',
        ]);

        $session->refresh();
        $this->assertEquals('Bagaimana cara menentukan rumusan masalah?', $session->title);
    }
}
