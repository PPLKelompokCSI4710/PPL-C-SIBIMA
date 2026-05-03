<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_user_can_see_login_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    // Tunggu hingga teks muncul di layar (penting untuk Vue/Inertia)
                ->waitForText('Masuk ke portal SIBIMA')
                ->assertSee('Masuk ke portal SIBIMA')
                ->type('email', 'maya@example.com')
                ->type('password', 'password')
                    // Pastikan nama tombol sesuai (Dusk mencari teks di dalam tombol)
                ->press('Masuk ke Portal')
                ->waitForLocation('/staff/dashboard')
                ->assertPathIs('/staff/dashboard');
        });
    }
}
