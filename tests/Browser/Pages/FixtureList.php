<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Page;

class FixtureList extends Page
{
    public function url(): string
    {
        return '/fixtures';
    }

    public function elements(): array
    {
        return [
            '@previousPage' => '.v-data-footer__icons-before > button',
            '@nextPage'     => '.v-data-footer__icons-after > button',
        ];
    }
}
