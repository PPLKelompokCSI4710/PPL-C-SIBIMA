<?php

namespace Tests\Browser;

use App\Models\AppSetting;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\AcademicAssistantUsage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Pbi31AiTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    /**
     * PBI 31: AI Quota Limits, Auto-Reset, and Admin Monitoring Dashboard
     */
    public function test_pbi_31_quota_reset_and_monitoring(): void
    {
        $mahasiswaUser = Mahasiswa::first()->user;
        $adminUser = User::where('email', 'admin@sibima.test')->first();

        // 1. Set daily quota to 2 questions
        AppSetting::set('ai_daily_quota', 2);
        AppSetting::set('gemini_mock_response', 'DYNAMIC');

        $this->browse(function (Browser $browser) use ($mahasiswaUser, $adminUser) {
            // Log in as student
            $browser->loginAs($mahasiswaUser)
                ->visit('/mahasiswa/dashboard')
                ->waitFor('@toggle-chat', 10)
                ->click('@toggle-chat')
                ->waitFor('@chat-input', 5)
                
                // Ask Question 1
                ->waitUntil('!document.querySelector(\'[dusk="chat-input"]\').disabled', 10)
                ->type('@chat-input', 'Apa itu metode kualitatif?')
                ->click('@send-button')
                ->waitForText('Metode kualitatif adalah metode penelitian yang fokus', 10)

                // Ask Question 2
                ->waitUntil('!document.querySelector(\'[dusk="chat-input"]\').disabled', 10)
                ->type('@chat-input', 'Bagaimana langkah menyusunnya?')
                ->click('@send-button')
                ->waitForText('Langkah menyusun metode kualitatif meliputi', 10)

                // Verify quota is exhausted and input is disabled
                ->waitForText('Kuota bertanya harian Anda telah habis. Input dinonaktifkan', 10)
                ->assertDisabled('@chat-input');

            // 2. Simulate daily auto-reset scheduler (date rolls over to the next day)
            // We set the date of today's usage records to yesterday
            AcademicAssistantUsage::query()->update(['date' => today()->subDay()]);

            // Refresh the page and open chat widget again
            $browser->refresh()
                ->waitFor('@toggle-chat', 10)
                ->click('@toggle-chat')
                ->waitFor('@chat-input', 5)
                ->waitUntil('!document.querySelector(\'[dusk="chat-input"]\').disabled', 10)
                ->assertEnabled('@chat-input')
                ->assertDontSee('Kuota bertanya harian Anda telah habis');

            // 3. Admin dashboard monitoring stats
            $browser->loginAs($adminUser)
                ->visit('/admin/academic-assistant')
                ->waitForText('AI Academic Assistant — Monitoring', 15)
                ->assertSee('Permintaan Hari Ini')
                ->assertSee('Total Semua Waktu')
                ->assertSee('Kuota Harian')
                ->visit('about:blank');
        });
    }
}
