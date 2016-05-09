<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use Tests\TestCase;
use App\Models\Club;

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
        $club = factory(Club::class)->create();
        $clubId = $club->id;
        $clubName = $club->club;

        // Brand new club
        $newClubName = 'New ' . $clubName;
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($newClubName, 'club')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Club added!')
            ->seeInDatabase('clubs', ['id' => $clubId + 1, 'club' => $newClubName]);

        // Already existing club
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($clubName, 'club')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The club already exists.')
            ->seeInDatabase('clubs', ['id' => $clubId, 'club' => $clubName]);
    }

    public function testEditClub()
    {
        $this->be($this->getFakeUser());

        /** @var Club $club */
        $club = factory(Club::class)->create();
        $clubId = $club->id;
        $clubName = $club->club;

        $newClubName = 'New ' . $clubName;
        $this->seeInDatabase('clubs', ['id' => $clubId, 'club' => $clubName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$clubId]))
            ->type($newClubName, 'club')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Club updated!')
            ->seeInDatabase('clubs', ['id' => $clubId, 'club' => $newClubName]);
        $clubName = $newClubName;

        $anotherClub = factory(Club::class)->create();
        $anotherClubName = $anotherClub->club;

        // Already existing club
        $this->seeInDatabase('clubs', ['id' => $clubId, 'club' => $clubName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$clubId]))
            ->type($anotherClubName, 'club')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$clubId]))
            ->seeInElement('.alert.alert-danger', 'The club already exists.')
            ->seeInDatabase('clubs', ['id' => $clubId, 'club' => $clubName]);
    }

    public function testShowClub()
    {
        $this->be($this->getFakeUser());

        /** @var Club $club */
        $club = factory(Club::class)->create();
        $clubId = $club->id;
        $clubName = $club->club;

        $this->visit(route(self::BASE_ROUTE . '.show', [$clubId]))
            ->seeInElement('tbody tr td:nth-child(1)', $clubId)
            ->seeInElement('tbody tr td:nth-child(2)', $clubName);
    }

    public function testDeleteClub()
    {
        $this->be($this->getFakeUser());

        /** @var Club $club */
        $club = factory(Club::class)->create();
        $clubId = $club->id;
        $clubName = $club->club;

        $this->seeInDatabase('clubs', ['id' => $clubId, 'club' => $clubName])
            ->call('DELETE', route(self::BASE_ROUTE . '.destroy', [$clubId]))
            ->isRedirect(route(self::BASE_ROUTE . '.index'));
        $this->dontSeeInDatabase('clubs', ['id' => $clubId]);
    }
}
