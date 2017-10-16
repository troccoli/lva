<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Page;

abstract class BasePage extends Page
{
    public $breadcrumb = '#breadcrumbs li.active';
    public $pageNavigation = 'div.pagination';

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
            '@form-errors'          => '#form-errors',
            '@submit-button'        => 'input[type="submit"]',
        ];
    }
}