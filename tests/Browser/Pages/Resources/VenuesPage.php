<?php

namespace Tests\Browser\Pages\Resources;

use Laravel\Dusk\Browser;

class VenuesPage extends BaseResourcePage
{
    protected $baseRoute = 'venues';

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return $this->indexUrl();
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
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@venue-error'  => '#venue-field p.help-block',
            '@directions-error'  => '#directions-field p.help-block',
            '@postcode-error'  => '#postcode-field p.help-block',
        ];
    }
}
