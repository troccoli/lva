<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class ForgotPasswordPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('password.request', [], false);
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url())
            ->assertSeeIn($this->breadcrumb, 'Reset Password');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@email-error' => '#email-field span.help-block',
        ];
    }
}
