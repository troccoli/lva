<?php

namespace Tests\Browser\Pages\Resources;

use Laravel\Dusk\Browser;

class DivisionsPage extends BaseResourcePage
{
    protected $baseRoute = 'divisions';

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
            '@season-id-error' => '#season-id-field p.help-block',
            '@division-error'  => '#division-field p.help-block',
        ];
    }
}
