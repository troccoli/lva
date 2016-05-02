<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:58
 */

namespace Admin;

use Tests\TestCase;

class DataManagementTest extends TestCase
{
    private $admin;

    public function testRedirectToLoginIfNotAdmin()
    {
        $this->visit(route('admin::dataManagement'))
            ->seePageIs(route('login'));
    }

    public function testBreadcrumbs()
    {
        $this->be($this->admin);

        $this->breadcrumbsTests('admin::dataManagement', 'Data Management');
    }

    public function testSeasonsTableButton()
    {
        $this->be($this->admin);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Seasons', route('admin.data-management.seasons.index'));
    }

    public function testDivisionsTableButton()
    {
        $this->be($this->admin);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Divisions', route('admin.data-management.divisions.index'));
    }

    public function testVenuesTableButton()
    {
        $this->be($this->admin);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Venues', route('admin.data-management.venues.index'));
    }

    public function testClubsTableButton()
    {
        $this->be($this->admin);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Clubs', route('admin.data-management.clubs.index'));
    }

    public function testTeamsTableButton()
    {
        $this->be($this->admin);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Teams', route('admin.data-management.teams.index'));
    }

    public function testRolesTableButton()
    {
        $this->be($this->admin);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Roles', route('admin.data-management.roles.index'));
    }

    public function testFixturesTableButton()
    {
        $this->be($this->admin);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Fixtures', route('admin.data-management.fixtures.index'));
    }

    public function testAvailableAppointmentsTableButton()
    {
        $this->be($this->admin);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Available appointments', route('admin.data-management.available-appointments.index'));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->admin = $this->getFakeUser();
    }
}
