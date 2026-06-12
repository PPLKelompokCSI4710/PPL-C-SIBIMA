<?php

namespace Tests\Browser;

use App\Models\AppSetting;
use App\Models\User;
use App\Models\Mahasiswa;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Pbi30AiTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    /**
     * PBI 30: AI Assistant Multi-turn & Session Management
     */
    public function test_pbi_30_ai_multi_turn_and_sessions(): void
    {
        $mahasiswaUser = Mahasiswa::first()->user;

        // Set mock to DYNAMIC response
        AppSetting::set('gemini_mock_response', 'DYNAMIC');

        $this->browse(function (Browser $browser) use ($mahasiswaUser) {
            // Log in and open chat
            $browser->loginAs($mahasiswaUser)
                ->visit('/mahasiswa/dashboard')
                ->waitFor('@toggle-chat', 10)
                ->click('@toggle-chat')
                ->waitFor('@chat-input', 5)
                
                // TC.AI.30.001 - Multi-turn conversation context
                // Turn 1
                ->waitUntil('!document.querySelector(\'[dusk="chat-input"]\').disabled', 10)
                ->type('@chat-input', 'Apa itu metode kualitatif?')
                ->click('@send-button')
                ->waitForText('Metode kualitatif adalah metode penelitian yang fokus', 10)

                // Turn 2: asks for steps (refers to previous context)
                ->waitUntil('!document.querySelector(\'[dusk="chat-input"]\').disabled', 10)
                ->type('@chat-input', 'Bagaimana langkah menyusunnya?')
                ->click('@send-button')
                ->waitForText('Langkah menyusun metode kualitatif meliputi', 10)

                // TC.AI.30.002 - Session history / new session
                // Open history panel
                ->waitFor('@history-button', 5)
                ->click('@history-button')
                ->waitFor('@new-session-button', 5)
                
                // Click new session button
                ->click('@new-session-button')
                
                // Verify new session starts with greeting
                ->waitForText('Sesi baru telah dimulai. Ada yang bisa saya bantu', 10)
                ->visit('about:blank');
        });
    }
}
