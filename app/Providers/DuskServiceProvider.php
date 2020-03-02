<?php

namespace App\Providers;

use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Browser::macro('vuetifySelect', function (string $selector, string $value) {
            $xpath = "//div[contains(concat(' ', normalize-space(@class), ' '), 'v-menu__content') and contains(concat(' ', normalize-space(@class), ' '), 'menuable__content__active')]//div[text()='$value']";
            $this->with("$selector", function (Browser $element) {
                $element->click('.v-select');
            })->waitFor('.menuable__content__active')
                ->driver->findElement(WebDriverBy::xpath($xpath))->click();
            $this->waitUntilMissing('.menuable__content__active');

            return $this;
        });
    }
}
