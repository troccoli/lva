<?php

namespace Tests\Browser\Pages\Resources;

use Tests\Browser\Pages\BasePage;

abstract class BaseResourcePage extends BasePage
{
    public $pageNavigation = 'div.pagination';
    public $resourcesListTable = '#resources-list';
    protected $baseRoute;

    /**
     * @return string
     */
    public function indexUrl()
    {
        return route($this->baseRoute . '.index', [], false);
    }

    /**
     * @return string
     */
    public function createUrl()
    {
        return route($this->baseRoute . '.create', [], false);
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function showUrl($id)
    {
        return route($this->baseRoute . '.show', [$id], false);
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function editUrl($id)
    {
        return route($this->baseRoute . '.edit', [$id], false);
    }
}
