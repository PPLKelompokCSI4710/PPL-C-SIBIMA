<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginGagalTest extends DuskTestCase
{
    public function test_login_gagal(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/login')
                ->type('input[type="email"]', 'mahasiswa@sibima.test')
                ->type('input[type="password"]', 'password_salah')
                ->press('LOG IN')

                ->waitForLocation('/login')
                ->assertPathIs('/login');
        });
    }
}
