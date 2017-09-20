<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Page as BasePage;

abstract class Page extends BasePage
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
            '@breadcrumb' => '.breadcrumbs .active',
        ];
    }
}
