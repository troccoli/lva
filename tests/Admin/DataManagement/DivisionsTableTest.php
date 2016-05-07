<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use App\Models\Division;
use App\Models\Season;
use Tests\TestCase;

class DivisionsTableTest extends TestCase
{
    const BASE_ROUTE = 'admin.data-management.divisions';

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
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Divisions');
    }

    public function testAddDivision()
    {
        $this->be($this->getFakeUser());

        /** @var Division $division */
        $division = factory(Division::class)->create();
        $divisionId = $division->id;
        $seasonId = $division->season_id;
        $divisionName = $division->division;

        // Brand new division
        $newDivisionName = 'New ' . $divisionName;
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($seasonId, 'season_id')
            ->type($newDivisionName, 'division')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Division added!')
            ->seeInDatabase('divisions', ['id' => $divisionId + 1, 'season_id' => $seasonId, 'division' => $newDivisionName]);

        // Already existing division in the same season
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($seasonId, 'season_id')
            ->type($divisionName, 'division')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The division already exists in the same season.')
            ->seeInDatabase('divisions', ['id' => $divisionId, 'season_id' => $seasonId, 'division' => $divisionName]);
    }

    public function testEditDivision()
    {
        $this->be($this->getFakeUser());

        /** @var Division $division */
        $division = factory(Division::class)->create();
        $divisionId = $division->id;
        $seasonId = $division->season_id;
        $divisionName = $division->division;

        // Change the name of the division
        $newDivisionName = 'New ' . $divisionName;
        $this->seeInDatabase('divisions', ['id' => $divisionId, 'season_id' => $seasonId, 'division' => $divisionName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$divisionId]))
            ->type($newDivisionName, 'division')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Division updated!')
            ->seeInDatabase('divisions', ['id' => $divisionId, 'season_id' => $seasonId, 'division' => $newDivisionName]);
        $divisionName = $newDivisionName;

        // Already existing division in the same season
        /** @var Division $anotherDivision */
        $anotherDivision = factory(Division::class)->create(['season_id' => $seasonId]);
        $anotherDivisionName = $anotherDivision->division;

        $this->seeInDatabase('divisions', ['id' => $divisionId, 'season_id' => $seasonId, 'division' => $divisionName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$divisionId]))
            ->type($anotherDivisionName, 'division')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$divisionId]))
            ->seeInElement('.alert.alert-danger', 'The division already exists in the same season.')
            ->seeInDatabase('divisions', ['id' => $divisionId, 'season_id' => $seasonId, 'division' => $divisionName]);

        // Move division to a different season
        $anotherSeason = factory(Season::class)->create();
        $anotherSeasonId = $anotherSeason->id;

        $this->seeInDatabase('divisions', ['id' => $divisionId, 'season_id' => $seasonId, 'division' => $divisionName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$divisionId]))
            ->select($anotherSeasonId, 'season_id')
            ->type($divisionName, 'division')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Division updated!')
            ->seeInDatabase('divisions', ['id' => $divisionId, 'season_id' => $anotherSeasonId, 'division' => $newDivisionName]);
    }

    public function testShowDivision()
    {
        $this->be($this->getFakeUser());

        /** @var Division $division */
        $division = factory(Division::class)->create();
        $divisionId = $division->id;
        $seasonId = $division->season_id;
        $divisionName = $division->division;

        /** @var Season $season */
        $season = Season::find($seasonId);
        $seasonName = $season->season;

        $this->visit(route(self::BASE_ROUTE . '.show', [$divisionId]))
            ->seeInElement('tbody tr td:nth-child(1)', $divisionId)
            ->seeInElement('tbody tr td:nth-child(2)', $seasonName)
            ->seeInElement('tbody tr td:nth-child(3)', $divisionName);
    }

    public function testDeleteDivision()
    {
        $this->be($this->getFakeUser());

        /** @var Division $division */
        $division = factory(Division::class)->create();
        $divisionId = $division->id;
        $seasonId = $division->season_id;
        $divisionName = $division->division;

        $this->seeInDatabase('divisions', ['id' => $divisionId, 'season_id' => $seasonId, 'division' => $divisionName])
            ->call('DELETE', route(self::BASE_ROUTE . '.destroy', [$divisionId]))
            ->isRedirect(route(self::BASE_ROUTE . '.index'));
        $this->dontSeeInDatabase('divisions', ['id' => $divisionId]);
    }
}
