<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use LVA\Models\Team;
use Tests\TestCase;
use LVA\Models\Club;

class ClubsTableTest extends TestCase
{
    const BASE_ROUTE = 'admin.data-management.clubs';

    public function testRedirectIfNotAdmin()
    {
        $this->visit(route(self::BASE_ROUTE . '.index'))
            ->seePageIs(route('login'));

        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->seePageIs(route('login'));

        $this->visit(route(self::BASE_ROUTE . '.show', [1]))
            ->seePageIs(route('login'));

        $this->visit(route(self::BASE_ROUTE . '.edit', [1]))
            ->seePageIs(route('login'));

        $this->call('POST', route(self::BASE_ROUTE . '.store'));
        $this->assertResponseStatus(302);

        $this->call('DELETE', route(self::BASE_ROUTE . '.destroy', [1]));
        $this->assertResponseStatus(302);

        $this->call('PUT', route(self::BASE_ROUTE . '.update', [1]));
        $this->assertResponseStatus(302);
    }

    public function testBreadcrumbs()
    {
        $this->be($this->getFakeUser());
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Clubs');
    }

    public function testAddClub()
    {
        $this->be($this->getFakeUser());

        /** @var Club $club */
        $club = factory(Club::class)->make();

        // Brand new club
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($club->club, 'club')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Club added!')
            ->seeInDatabase('clubs', [
                'id'   => 1,
                'club' => $club->club,
            ]);

        // Already existing club
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($club->club, 'club')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The club already exists.')
            ->seeInDatabase('clubs', [
                'id'   => 1,
                'club' => $club->club,
            ]);
    }

    public function testEditClub()
    {
        $this->be($this->getFakeUser());

        /** @var Club $club */
        $club = factory(Club::class)->create();

        // Don't change anything
        $this->seeInDatabase('clubs', [
            'id'   => $club->id,
            'club' => $club->club,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$club->id]))
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Club updated!')
            ->seeInDatabase('clubs', [
                'id'   => $club->id,
                'club' => $club->club,
            ]);

        /** @var Club $newClub */
        $newClub = factory(Club::class)->make();

        $this->seeInDatabase('clubs', [
            'id'   => $club->id,
            'club' => $club->club,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$club->id]))
            ->type($newClub->club, 'club')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Club updated!')
            ->seeInDatabase('clubs', [
                'id'   => $club->id,
                'club' => $newClub->club,
            ]);
        $club->club = $newClub->club;
        unset($newClub);

        /** @var Club $newClub */
        $newClub = factory(Club::class)->create();

        // Already existing club
        $this->seeInDatabase('clubs', [
            'id'   => $club->id,
            'club' => $club->club,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$club->id]))
            ->type($newClub->club, 'club')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$club->id]))
            ->seeInElement('.alert.alert-danger', 'The club already exists.')
            ->seeInDatabase('clubs', [
                'id'   => $club->id,
                'club' => $club->club,
            ]);
    }

    public function testShowClub()
    {
        $this->be($this->getFakeUser());

        /** @var Club $club */
        $club = factory(Club::class)->create();

        $this->visit(route(self::BASE_ROUTE . '.show', [$club->id]))
            ->seeInElement('tbody tr td:nth-child(1)', $club->id)
            ->seeInElement('tbody tr td:nth-child(2)', $club->club);
    }

    public function testDeleteClub()
    {
        $this->be($this->getFakeUser());

        /** @var Club $club */
        $club = factory(Club::class)->create();
        $clubId = $club->id;

        $this->seeInDatabase('clubs', [
            'id'   => $club->id,
            'club' => $club->club,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$club->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Club deleted!')
            ->dontSeeInDatabase('clubs', ['id' => $clubId]);

        // Delete a clubs with teams
        /** @var Club $club */
        $club = factory(Club::class)->create();
        /** @var Team $team */
        $team = factory(Team::class)->create(['club_id' => $club->id]);
        $this->seeInDatabase('clubs', [
            'id'   => $club->id,
            'club' => $club->club,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$club->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-danger', 'Cannot delete because they are existing teams in this club.')
            ->seeInDatabase('clubs', [
                'id'   => $club->id,
                'club' => $club->club,
            ]);
    }
}
