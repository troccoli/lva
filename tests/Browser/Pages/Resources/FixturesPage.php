<?php

namespace Tests\Browser\Pages\Resources;

use Laravel\Dusk\Browser;

class FixturesPage extends BaseResourcePage
{
    protected $baseRoute = 'fixtures';

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
     * @param Browser $browser
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
            '@division-id-error'  => '#division-id-field p.help-block',
            '@match-number-error' => '#match-number-field p.help-block',
            '@match-date-error'   => '#match-date-field p.help-block',
            '@warm-up-time-error' => '#warm-up-time-field p.help-block',
            '@start-time-error'   => '#start-time-field p.help-block',
            '@home-team-id-error' => '#home-team-id-field p.help-block',
            '@away-team-id-error' => '#away-team-id-field p.help-block',
            '@venue-id-error'     => '#venue-id-field p.help-block',
            '@notes-error'        => '#notes-field p.help-block',
        ];
    }
}
