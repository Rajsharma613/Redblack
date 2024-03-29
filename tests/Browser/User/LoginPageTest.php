<?php

namespace Tests\Browser\User;

use Laravel\Dusk\Browser;
use Tests\Browser\BrowserTestCase;

class LoginPageTest extends BrowserTestCase
{
    public function testRedirect()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertPathIs('/login')
                    ->assertSee('GameAP');
        });
    }

    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('login', 'admin')
                ->type('password', 'fpwPOuZD')
                ->press('Login')
                ->assertPathIs('/home');
        });
    }
}
