<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use Tests\TestCase;

class FixturesTableTest extends TestCase
{
    public function testRedirectToLoginIfNotAdmin()
    {
        $this->visit(route('admin.data-management.fixtures.index'))
            ->seePageIs(route('login'));
    }

    public function testBreadcrumbs()
    {
        $this->be($this->getFakeUser());
        $this->breadcrumbsTests('admin.data-management.fixtures.index', 'Fixtures');
    }

}