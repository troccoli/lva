<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class LoginPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('login', [], false);
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser $browser
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url())
            ->assertSeeIn($this->breadcrumb, 'Login');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@email-error'    => '#email-field span.help-block',
            '@password-error' => '#password-field span.help-block',
        ];
    }
}
