<?php

namespace LVA\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('required_headers', \LVA\Validators\CustomValidators::class . '@requiredHeaders');
        Validator::replacer('required_headers', \LVA\Validators\CustomValidators::class . '@requiredHeadersMessage');
        Validator::extend('uk_postcode', \LVA\Validators\CustomValidators::class . '@ukPostcode');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'local') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(\Appzcoder\CrudGenerator\CrudGeneratorServiceProvider::class);
            $this->app->register(\Laracademy\Commands\MakeServiceProvider::class);
            $this->app->register(DuskServiceProvider::class);
        }

        if ($this->app->environment() == 'testing') {
            $this->app->bind(\Faker\Generator::class, function () {
                return \Faker\Factory::create(config('app.faker_locale'));
            });
            $this->app->register(DuskServiceProvider::class);
        }
    }
}
