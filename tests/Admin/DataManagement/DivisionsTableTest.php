<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use LVA\Models\Fixture;
use Tests\TestCase;
use LVA\Models\Division;
use LVA\Models\Season;

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
        $division = factory(Division::class)->make();

        // Brand new division
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($division->season_id, 'season_id')
            ->type($division->division, 'division')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Division added!')
            ->seeInDatabase('divisions', [
                'id'        => 1,
                'season_id' => $division->season_id,
                'division'  => $division->division,
            ]);

        // Already existing division in the same season
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($division->season_id, 'season_id')
            ->type($division->division, 'division')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The division already exists in the same season.')
            ->seeInDatabase('divisions', [
                'id'        => 1,
                'season_id' => $division->season_id,
                'division'  => $division->division,
            ]);
    }

    public function testEditDivision()
    {
        $this->be($this->getFakeUser());

        /** @var Division $division */
        $division = factory(Division::class)->create();

        /** @var Division $newDivision */
        $newDivision = factory(Division::class)->make();

        // Change the name of the division
        $this->seeInDatabase('divisions', [
            'id'        => $division->id,
            'season_id' => $division->season_id,
            'division'  => $division->division,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$division->id]))
            ->type($newDivision->division, 'division')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Division updated!')
            ->seeInDatabase('divisions', [
                'id'        => $division->id,
                'season_id' => $division->season_id,
                'division'  => $newDivision->division,
            ]);
        $division->division = $newDivision->division;
        unset($newDivision);

        // Move division to a different season
        $season = factory(Season::class)->create();

        $this->seeInDatabase('divisions', [
            'id'        => $division->id,
            'season_id' => $division->season_id,
            'division'  => $division->division,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$division->id]))
            ->select($season->id, 'season_id')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Division updated!')
            ->seeInDatabase('divisions', [
                'id'        => $division->id,
                'season_id' => $season->id,
                'division'  => $division->division,
            ]);
        $division->season_id = $season->id;
        unset($season);

        // Already existing division in the same season
        /** @var Division $newDivision */
        $newDivision = factory(Division::class)->create(['season_id' => $division->season_id]);

        $this->seeInDatabase('divisions', [
            'id'        => $division->id,
            'season_id' => $division->season_id,
            'division'  => $division->division,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$division->id]))
            ->type($newDivision->division, 'division')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$division->id]))
            ->seeInElement('.alert.alert-danger', 'The division already exists in the same season.')
            ->seeInDatabase('divisions', [
                'id'        => $division->id,
                'season_id' => $division->season_id,
                'division'  => $division->division,
            ]);
    }

    public function testShowDivision()
    {
        $this->be($this->getFakeUser());

        /** @var Division $division */
        $division = factory(Division::class)->create();

        $this->visit(route(self::BASE_ROUTE . '.show', [$division->id]))
            ->seeInElement('tbody tr td:nth-child(1)', $division->id)
            ->seeInElement('tbody tr td:nth-child(2)', $division->season)
            ->seeInElement('tbody tr td:nth-child(3)', $division->division);
    }

    public function testDeleteDivision()
    {
        $this->be($this->getFakeUser());

        /** @var Division $division */
        $division = factory(Division::class)->create();
        $divisionId = $division->id;

        $this->seeInDatabase('divisions', [
            'id'        => $division->id,
            'season_id' => $division->season_id,
            'division'  => $division->division,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$division->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Division deleted!')
            ->dontSeeInDatabase('divisions', ['id' => $divisionId]);

        // Delete division with fixtures
        /** @var Division $division */
        $division = factory(Division::class)->create();
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create(['division_id' => $division->id]);

        $this->seeInDatabase('divisions', [
            'id'        => $division->id,
            'season_id' => $division->season_id,
            'division'  => $division->division,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$division->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-danger', 'Cannot delete because they are existing fixtures in this division.')
            ->seeInDatabase('divisions', [
                'id'        => $division->id,
                'season_id' => $division->season_id,
                'division'  => $division->division,
            ]);
    }
}
