<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::include('partials.form.submit-button', 'submitButton');

        Blade::include('partials.form.error-field', 'errorField');
        Blade::include('partials.form.text-field', 'textField');
        Blade::include('partials.form.hidden-field', 'hiddenField');
        Blade::include('partials.form.email-field', 'emailField');
        Blade::include('partials.form.password-field', 'passwordField');
        Blade::include('partials.form.checkbox-field', 'checkboxField');
    }
}
