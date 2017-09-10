<?php

namespace Tests\Browser\Pages;

use Illuminate\Support\Facades\Auth;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class DataManagementPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('data-management');
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
        if (Auth::guest()) {
            $browser->assertRouteIs('login');
        } else {
            $browser->assertPathIs($this->url())
                ->assertSeeIn('@breadcrumb', 'Data Management');
        }

    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '#selector',
        ];
    }
}
