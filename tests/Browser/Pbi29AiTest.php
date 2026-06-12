<?php

namespace Tests\Browser;

use App\Models\AppSetting;
use App\Models\User;
use App\Models\Mahasiswa;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Pbi29AiTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    /**
     * PBI 29: AI Assistant Context and Error Handling Tests
     */
    public function test_pbi_29_ai_assistant(): void
    {
        $mahasiswaUser = Mahasiswa::first()->user;

        // Set mock to DYNAMIC response
        AppSetting::set('gemini_mock_response', 'DYNAMIC');

        $this->browse(function (Browser $browser) use ($mahasiswaUser) {
            // 1. Context-aware relevan
            $browser->loginAs($mahasiswaUser)
                ->visit('/mahasiswa/dashboard')
                ->waitFor('@toggle-chat', 10)
                ->click('@toggle-chat')
                ->waitFor('@chat-input', 5)
                ->waitUntil('!document.querySelector(\'[dusk="chat-input"]\').disabled', 10)
                ->type('@chat-input', 'Apa itu metode kualitatif?')
                ->click('@send-button')
                ->waitForText('metode penelitian yang fokus pada pemahaman mendalam', 10)

            // 2. Pertanyaan di luar topik
                ->waitUntil('!document.querySelector(\'[dusk="chat-input"]\').disabled', 10)
                ->type('@chat-input', 'Bagaimana cara memasak rendang?')
                ->click('@send-button')
                ->waitForText('Maaf, saya hanya dapat membantu Anda dalam konteks pendidikan', 10);

            // 3. Penanganan Error API Down
            AppSetting::set('gemini_mock_response', 'FORCE_TIMEOUT');

            $browser->waitUntil('!document.querySelector(\'[dusk="chat-input"]\').disabled', 10)
                ->type('@chat-input', 'Bagaimana cara menyusun bab 1?')
                ->click('@send-button')
                ->waitFor('.bg-red-50', 10)
                ->assertSee('Koneksi ke Gemini API gagal')
                ->visit('about:blank');
        });
    }
}
