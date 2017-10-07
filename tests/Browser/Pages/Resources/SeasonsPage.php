<?php

namespace Tests\Browser\Pages\Resources;

use Laravel\Dusk\Browser;

class SeasonsPage extends BaseResourcePage
{
    protected $baseRoute = 'seasons';

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
            '@season-error'  => '#season-field p.help-block',
        ];
    }
}
