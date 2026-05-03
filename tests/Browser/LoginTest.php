<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_tc_login_001_user_can_login_with_registered_email_and_password(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visitRoute('login')
                ->assertSee('Email')
                ->assertSee('Password')
                ->type('#email', $user->email)
                ->type('#password', 'password')
                // PrimaryButton uses CSS uppercase; WebDriver getText() is "LOG IN", not "Log in".
                ->waitFor('form button')
                ->click('form button')
                ->waitForRoute('dashboard')
                ->assertPathIs('/dashboard')
                ->assertSee("You're logged in!");
        });
    }

    public function test_tc_login_002_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visitRoute('login')
                ->type('#email', $user->email)
                ->type('#password', 'wrong-password')
                ->waitFor('form button')
                ->click('form button')
                ->waitForText(Lang::get('auth.failed'), 10)
                ->assertPathIs('/login')
                ->assertSee(Lang::get('auth.failed'));
        });
    }
}
