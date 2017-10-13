<?php

namespace Tests\Browser\Pages\Resources;

use Laravel\Dusk\Browser;

class TeamsPage extends BaseResourcePage
{
    protected $baseRoute = 'teams';

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
            '@club-id-error'  => '#club-id-field p.help-block',
            '@team-error'  => '#team-field p.help-block',
            '@trigram-error'  => '#trigram-field p.help-block',
        ];
    }
}
