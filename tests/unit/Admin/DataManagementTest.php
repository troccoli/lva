<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:58
 */

namespace Admin;

use LVA\User;
use Tests\TestCase;

class DataManagementTest extends TestCase
{
    public function testRedirectToLoginIfNotAdmin()
    {
        $this->visit(route('admin::dataManagement'))
            ->seePageIs(route('login'));
    }

    public function testBreadcrumbs()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->breadcrumbsTests('admin::dataManagement', 'Data Management');
    }

    public function testSeasonsTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Seasons', route('admin.data-management.seasons.index'));
    }

    public function testDivisionsTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Divisions', route('admin.data-management.divisions.index'));
    }

    public function testVenuesTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Venues', route('admin.data-management.venues.index'));
    }

    public function testClubsTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Clubs', route('admin.data-management.clubs.index'));
    }

    public function testTeamsTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Teams', route('admin.data-management.teams.index'));
    }

    public function testRolesTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Roles', route('admin.data-management.roles.index'));
    }

    public function testFixturesTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Fixtures', route('admin.data-management.fixtures.index'));
    }

    public function testAvailableAppointmentsTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('admin::dataManagement'))
            ->seeLink('Available appointments', route('admin.data-management.available-appointments.index'));
    }
}
