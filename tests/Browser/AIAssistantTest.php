<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Http;

class AIAssistantTest extends DuskTestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;

    protected $seed = true;

    /**
     * TC.AI.29.001 - Context-aware pertanyaan relevan
     */
    public function test_ai_menjawab_dengan_konteks(): void
    {
        $mahasiswaUser = \App\Models\Mahasiswa::first()->user;

        $this->browse(function (Browser $browser) use ($mahasiswaUser) {
            $browser->loginAs($mahasiswaUser)
                    ->visit('/mahasiswa/ai-assistant')
                    ->pause(1000)
                    ->waitFor('@chat-input', 5)
                    ->type('@chat-input', 'Apa itu metode kualitatif?')
                    ->click('@send-button')
                    ->pause(2000)
                    // The actual response might be mocked or real. If real, it takes time.
                    // We just assert it shows a bot message container
                    ->waitFor('.prose', 10)
                    ->assertSee('kualitatif'); // Assume the bot responds with something about qualitative
        });
    }

    /**
     * TC.AI.31.001 - Limit kuota harian habis
     */
    public function test_input_dinonaktifkan_jika_kuota_habis(): void
    {
        $mahasiswaUser = \App\Models\Mahasiswa::first()->user;
        
        // Drain quota
        $usage = \App\Models\AcademicAssistantUsage::firstOrCreate([
            'user_id' => $mahasiswaUser->id,
            'date' => date('Y-m-d')
        ], [
            'request_count' => 0
        ]);
        
        $usage->update(['request_count' => 20]); // Assuming 20 is max

        $this->browse(function (Browser $browser) use ($mahasiswaUser) {
            $browser->loginAs($mahasiswaUser)
                    ->visit('/mahasiswa/ai-assistant')
                    ->pause(1000)
                    ->assertSee('Batas Harian Tercapai')
                    ->assertDisabled('@chat-input')
                    ->assertDisabled('@send-button');
        });
    }
}
