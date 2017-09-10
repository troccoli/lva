<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:58
 */

namespace Admin;

use LVA\User;
use Tests\OldStyleTestCase;

class DataManagementOldStyleTest extends OldStyleTestCase
{




    public function testDivisionsTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('data-management'))
            ->seeLink('Divisions', route('divisions.index'));
    }

    public function testVenuesTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('data-management'))
            ->seeLink('Venues', route('venues.index'));
    }

    public function testClubsTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('data-management'))
            ->seeLink('Clubs', route('clubs.index'));
    }

    public function testTeamsTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('data-management'))
            ->seeLink('Teams', route('teams.index'));
    }

    public function testRolesTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('data-management'))
            ->seeLink('Roles', route('roles.index'));
    }

    public function testFixturesTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('data-management'))
            ->seeLink('Fixtures', route('fixtures.index'));
    }

    public function testAvailableAppointmentsTableButton()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        $this->visit(route('data-management'))
            ->seeLink('Available appointments', route('available-appointments.index'));
    }
}
