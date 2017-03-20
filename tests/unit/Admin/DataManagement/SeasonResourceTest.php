<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use LVA\Models\Division;
use LVA\User;
use PhpParser\Node\Expr\AssignOp\Div;
use Tests\TestCase;
use LVA\Models\Season;

class SeasonResourceTest extends TestCase
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
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Seasons');
    }

    public function testAddSeason()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Season $season */
        $season = factory(Season::class)->make();

        // Brand new season
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($season->season, 'season')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Season added!')
            ->seeInDatabase('seasons', [
                'season' => $season->season,
            ]);

        // Already existing season
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($season->season, 'season')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The season already exists.')
            ->seeInDatabase('seasons', [
                'season' => $season->season,
            ]);
    }

    public function testEditSeason()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Season $season */
        $season = factory(Season::class)->create();

        // Don't change anything
        $this->seeInDatabase('seasons', [
            'id'     => $season->id,
            'season' => $season->season,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$season->id]))
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Season updated!')
            ->seeInDatabase('seasons', [
                'id'     => $season->id,
                'season' => $season->season,
            ]);

        /** @var Season $newSeason */
        $newSeason = factory(Season::class)->make();

        $this->seeInDatabase('seasons', [
            'id'     => $season->id,
            'season' => $season->season,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$season->id]))
            ->type($newSeason->season, 'season')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Season updated!')
            ->seeInDatabase('seasons', [
                'id'     => $season->id,
                'season' => $newSeason->season,
            ]);
        $season->season = $newSeason->season;
        unset($newSeason);

        /** @var Season $newSeason */
        $newSeason = factory(Season::class)->create();

        // Already existing season
        $this->seeInDatabase('seasons', [
            'id'     => $season->id,
            'season' => $season->season,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$season->id]))
            ->type($newSeason->season, 'season')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$season->id]))
            ->seeInElement('.alert.alert-danger', 'The season already exists.')
            ->seeInDatabase('seasons', [
                'id'     => $season->id,
                'season' => $season->season,
            ]);
    }

    public function testShowSeason()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Season $season */
        $season = factory(Season::class)->create();

        $this->visit(route(self::BASE_ROUTE . '.show', [$season->id]))
            ->seeInElement('tbody tr td:nth-child(1)', $season->id)
            ->seeInElement('tbody tr td:nth-child(2)', $season->season);
    }

    public function testDeleteSeason()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Season $season */
        $season = factory(Season::class)->create();
        $seasonId = $season->id;

        $this->seeInDatabase('seasons', [
            'id'     => $season->id,
            'season' => $season->season,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$season->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Season deleted!')
            ->dontSeeInDatabase('seasons', ['id' => $seasonId]);

        // Delete a season with divisions
        /** @var Season $season */
        $season = factory(Season::class)->create();
        /** @var Division $division */
        $division = factory(Division::class)->create(['season_id' => $season->id]);

        $this->seeInDatabase('seasons', [
            'id'     => $season->id,
            'season' => $season->season,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$season->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-danger', 'Cannot delete because they are existing divisions in this season.')
            ->seeInDatabase('seasons', [
                'id'     => $season->id,
                'season' => $season->season,
            ]);
    }
}
