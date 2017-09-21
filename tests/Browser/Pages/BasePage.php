<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Page;

abstract class BasePage extends Page
{
    protected $breadcrumb = '#breadcrumbs li.active';

    /**
     * Get the global element shortcuts for the site.
     *
     * @return array
     */
    public static function siteElements()
    {
        return [
            '@breadcrumb'           => '.breadcrumbs .active',
            '@success-notification' => '#flash-notification .alert.alert-success',
            '@form-errors'          => 'div.alert.alert-danger',
        ];
    }
}
