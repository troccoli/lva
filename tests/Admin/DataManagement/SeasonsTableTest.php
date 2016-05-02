<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use Tests\TestCase;

class SeasonsTableTest extends TestCase
{
    public function testRedirectToLoginIfNotAdmin()
    {
        $this->post(route('admin.data-management.seasons.store'))
            ->assertResponseStatus(302);
        $this->visit(route('admin.data-management.seasons.index'))
            ->seePageIs(route('login'));
        $this->visit(route('admin.data-management.seasons.create'))
            ->seePageIs(route('login'));
        $response = $this->call('DELETE', route('admin.data-management.seasons.destroy', [1]));
        $this->assertEquals(302, $response->status());
        $response = $this->call('PUT', route('admin.data-management.seasons.update', [1]));
        $this->assertEquals(302, $response->status());
        $this->visit(route('admin.data-management.seasons.show', [1]))
            ->seePageIs(route('login'));
        $this->visit(route('admin.data-management.seasons.edit', [1]))
            ->seePageIs(route('login'));
    }

    public function testBreadcrumbs()
    {
        $this->be($this->getFakeUser());
        $this->breadcrumbsTests('admin.data-management.seasons.index', 'Seasons');
    }

    public function testAddSeason()
    {

    }

    public function testEditSeason()
    {

    }

    public function testShowSeason()
    {

    }

    public function testDeleteSeason()
    {
        
    }
}
