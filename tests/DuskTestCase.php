<?php

namespace Tests;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    public function setUp()
    {
        parent::setUp();

        Browser::macro('scrollToElement', function ($element = null) {
            $this->script("$('html, body').animate({ scrollTop: $('$element').offset().top }, 0);");

            return $this;
        });

        Browser::macro('pressSubmit', function ($text) {
            $this->script("$('html, body').animate({ scrollTop: $('input[type=\"submit\"]').offset().top }, 0);");
            $this->press($text);

            return $this;
        });
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        return RemoteWebDriver::create(
            'http://127.0.0.1:9515', DesiredCapabilities::chrome()
        );
    }

    public function browse(\Closure $callback)
    {
        parent::browse($callback);
        static::$browsers->first()->driver->manage()->deleteAllCookies();
    }

    protected function newBrowser($driver)
    {
        return parent::newBrowser($driver)->maximize();
    }
}
