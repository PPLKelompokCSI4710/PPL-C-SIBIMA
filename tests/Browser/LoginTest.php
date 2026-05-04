<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * TC.Login.001 - Login berhasil
     */
    public function test_login(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/login')

                // pakai selector lebih aman
                ->type('input[type="email"]', 'mahasiswa@sibima.test')
                ->type('input[type="password"]', 'password')

                // tombol sesuai web kamu
                ->press('LOG IN')

                // tunggu redirect
                ->waitForLocation('/dashboard')

                // validasi sukses login
                ->assertPathIs('/dashboard');
        });
    }
}
