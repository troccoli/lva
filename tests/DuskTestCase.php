<?php

namespace Tests;

use App\Models\Venue;
use Closure;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'Site Administrator']);
        Permission::create(['name' => 'manage raw data']);
    }

    /**
     * @beforeClass
     */
    public static function prepare(): void
    {
        static::startChromeDriver();
    }

    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments([
            'disable-gpu',
            'headless',
            'no-sandbox',
            'ignore-ssl-errors',
            'whitelisted-ips=""',
            'window-size=1920,1080',
        ]);

        if (config('testing.use_selenium')) {
            return RemoteWebDriver::create(
                'http://selenium:4444/wd/hub', DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            ));
        }

        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(
            ChromeOptions::CAPABILITY, $options
        ));
    }

    public function browse(Closure $callback)
    {
        parent::browse($callback);
        static::$browsers->first()->driver->manage()->deleteAllCookies();
    }
}
