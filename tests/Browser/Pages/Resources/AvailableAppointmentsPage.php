<?php

namespace Tests\Browser\Pages\Resources;

use Laravel\Dusk\Browser;

class AvailableAppointmentsPage extends BaseResourcePage
{
    protected $baseRoute = 'available-appointments';

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
            '@fixture-id-error' => '#fixture-id-field p.help-block',
            '@role-id-error'    => '#role-id-field p.help-block',
        ];
    }
}
