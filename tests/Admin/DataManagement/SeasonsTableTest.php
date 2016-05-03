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

    /** @var Season $existingSeason */
    private $existingSeason = null;

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

        // Brand new season
        $newSeason = 'New ' . $this->existingSeason->season;
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($newSeason, 'season')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Season added!')
            ->seeInDatabase('seasons', ['id' => 2, 'season' => $newSeason]);

        // Already existing season
        $this->markTestIncomplete('Fix the validation for already existing season');
    }

    public function testEditSeason()
    {
        $this->be($this->getFakeUser());

        // Brand new season
        $newSeason = 'New ' . $this->existingSeason->season;
        $this->seeInDatabase('seasons', ['id' => 1, 'season' => $this->existingSeason->season])
            ->visit(route(self::BASE_ROUTE . '.edit', [1]))
            ->type($newSeason, 'season')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Season updated!')
            ->seeInDatabase('seasons', ['id' => 1, 'season' => $newSeason]);

        // Already existing season
        $this->markTestIncomplete('Fix the validation for already existing season');
    }

    public function testShowSeason()
    {
        $this->be($this->getFakeUser());

        $this->visit(route(self::BASE_ROUTE . '.show', [1]))
            ->seeInElement('tbody tr td:nth-child(1)', 1)
            ->seeInElement('tbody tr td:nth-child(2)', $this->existingSeason->season);
    }

    public function testDeleteSeason()
    {
        $this->be($this->getFakeUser());

        $this->seeInDatabase('seasons', ['id' => 1, 'season' => $this->existingSeason->season])
            ->call('DELETE', route(self::BASE_ROUTE . '.destroy', [1]))
            ->isRedirect(route(self::BASE_ROUTE . '.index'));
        $this->dontSeeInDatabase('seasons', ['id' => 1]);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->existingSeason = factory(Season::class)->create();
    }
}
