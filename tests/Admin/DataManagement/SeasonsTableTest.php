<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use App\Models\Season;
use Tests\TestCase;

class SeasonsTableTest extends TestCase
{
    const BASE_ROUTE = 'admin.data-management.seasons';

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
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Seasons');
    }

    public function testAddSeason()
    {
        $this->be($this->getFakeUser());

        /** @var Season $season */
        $season = factory(Season::class)->create();
        $seasonId = $season->id;
        $seasonName = $season->season;

        // Brand new season
        $newSeasonName = 'New ' . $seasonName;
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($newSeasonName, 'season')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Season added!')
            ->seeInDatabase('seasons', ['id' => $seasonId + 1, 'season' => $newSeasonName]);

        // Already existing season
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($seasonName, 'season')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The season already exists.')
            ->seeInDatabase('seasons', ['id' => $seasonId, 'season' => $seasonName]);
    }

    public function testEditSeason()
    {
        $this->be($this->getFakeUser());

        /** @var Season $season */
        $season = factory(Season::class)->create();
        $seasonId = $season->id;
        $seasonName = $season->season;

        $newSeasonName = 'New ' . $seasonName;
        $this->seeInDatabase('seasons', ['id' => $seasonId, 'season' => $seasonName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$seasonId]))
            ->type($newSeasonName, 'season')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Season updated!')
            ->seeInDatabase('seasons', ['id' => $seasonId, 'season' => $newSeasonName]);
        $seasonName = $newSeasonName;

        $anotherSeason = factory(Season::class)->create();
        $anotherSeasonName = $anotherSeason->season;

        // Already existing season
        $this->seeInDatabase('seasons', ['id' => $seasonId, 'season' => $seasonName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$seasonId]))
            ->type($anotherSeasonName, 'season')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$seasonId]))
            ->seeInElement('.alert.alert-danger', 'The season already exists.')
            ->seeInDatabase('seasons', ['id' => $seasonId, 'season' => $seasonName]);
    }

    public function testShowSeason()
    {
        $this->be($this->getFakeUser());

        /** @var Season $season */
        $season = factory(Season::class)->create();
        $seasonId = $season->id;
        $seasonName = $season->season;

        $this->visit(route(self::BASE_ROUTE . '.show', [$seasonId]))
            ->seeInElement('tbody tr td:nth-child(1)', $seasonId)
            ->seeInElement('tbody tr td:nth-child(2)', $seasonName);
    }

    public function testDeleteSeason()
    {
        $this->be($this->getFakeUser());

        /** @var Season $season */
        $season = factory(Season::class)->create();
        $seasonId = $season->id;
        $seasonName = $season->season;

        $this->seeInDatabase('seasons', ['id' => $seasonId, 'season' => $seasonName])
            ->call('DELETE', route(self::BASE_ROUTE . '.destroy', [$seasonId]))
            ->isRedirect(route(self::BASE_ROUTE . '.index'));
        $this->dontSeeInDatabase('seasons', ['id' => $seasonId]);
    }
}
