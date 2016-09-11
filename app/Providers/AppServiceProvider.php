<?php

namespace App\Providers;

use App\Validators\CustomValidators;
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
        Validator::extend('required_headers', CustomValidators::class . '@requiredHeaders');
        Validator::replacer('required_headers', CustomValidators::class . '@requiredHeadersMessage');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if($this->app->environment('local'))
        {
            $this->app->register('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');
            $this->app->register('Appzcoder\CrudGenerator\CrudGeneratorServiceProvider');
        }
    }
}
