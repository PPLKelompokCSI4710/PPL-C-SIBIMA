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
}
