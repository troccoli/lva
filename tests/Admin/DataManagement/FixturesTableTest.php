<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use Faker\Generator;
use Tests\TestCase;
use App\Models\Fixture;
use App\Models\Division;
use App\Models\Team;
use App\Models\Venue;

class FixturesTableTest extends TestCase
{
    const BASE_ROUTE = 'admin.data-management.fixtures';

    /** @var Generator */
    private $faker;

    public function setUp()
    {
        parent::setUp();

        $this->faker = new Generator();
    }

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
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Fixtures');
    }

    public function testAddFixture()
    {
        $this->be($this->getFakeUser());

        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->make();

        // Brand new fixture
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($fixture->division_id, 'division_id')
            ->select($fixture->home_team_id, 'home_team_id')
            ->select($fixture->away_team_id, 'away_team_id')
            ->select($fixture->venue_id, 'venue_id')
            ->type($fixture->match_number, 'match_number')
            ->type($fixture->match_date, 'match_date')
            ->type($fixture->warm_up_time, 'warm_up_time')
            ->type($fixture->start_time, 'start_time')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Fixture added!')
            ->seeInDatabase('fixtures', [
                'id'           => 1,
                'division_id'  => $fixture->division_id,
                'home_team_id' => $fixture->home_team_id,
                'away_team_id' => $fixture->away_team_id,
                'venue_id'     => $fixture->venue_id,
                'match_number' => $fixture->match_number,
                'match_date'   => $fixture->match_date->format('Y-m-d'),
                'warm_up_time' => $fixture->warm_up_time,
                'start_time'   => $fixture->start_time,
            ]);

        // New fixture with same home and away team
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($fixture->division_id, 'division_id')
            ->select($fixture->home_team_id, 'home_team_id')
            ->select($fixture->home_team_id, 'away_team_id')
            ->select($fixture->venue_id, 'venue_id')
            ->type($fixture->match_number, 'match_number')
            ->type($fixture->match_date, 'match_date')
            ->type($fixture->warm_up_time, 'warm_up_time')
            ->type($fixture->start_time, 'start_time')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The away team cannot be the same as the home team.');

        // New fixture with same division, home and away teams of existing one
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($fixture->division_id, 'division_id')
            ->select($fixture->home_team_id, 'home_team_id')
            ->select($fixture->away_team_id, 'away_team_id')
            ->select($fixture->venue_id, 'venue_id')
            ->type($fixture->match_number, 'match_number')
            ->type($fixture->match_date, 'match_date')
            ->type($fixture->warm_up_time, 'warm_up_time')
            ->type($fixture->start_time, 'start_time')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The fixture for these two teams have already been added in this division.');

        // New fixture with same division and match number of existing one
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($fixture->division_id, 'division_id')
            ->select($fixture->home_team_id, 'home_team_id')
            ->select($fixture->away_team_id, 'away_team_id')
            ->select($fixture->venue_id, 'venue_id')
            ->type($fixture->match_number, 'match_number')
            ->type($fixture->match_date, 'match_date')
            ->type($fixture->warm_up_time, 'warm_up_time')
            ->type($fixture->start_time, 'start_time')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'There is already a match with the same number in this division.');
    }
}
