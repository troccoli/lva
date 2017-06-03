<?php

namespace LVA\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

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


        }

        if ($this->app->environment() == 'testing') {
            $this->app->bind(\Faker\Generator::class, function () {
                return \Faker\Factory::create(config('app.faker_locale'));
            });
        }
    }
}
