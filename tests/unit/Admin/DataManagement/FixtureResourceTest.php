<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use LVA\Models\AvailableAppointment;
use LVA\User;
use Tests\TestCase;
use LVA\Models\Fixture;

class FixtureResourceTest extends TestCase
{
    const BASE_ROUTE = 'admin.data-management.fixtures';

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
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Fixtures');
    }

    public function testAddFixture()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

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
            ->type($fixture->notes, 'notes')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Fixture added!')
            ->seeInDatabase('fixtures', [
                'division_id'  => $fixture->division_id,
                'home_team_id' => $fixture->home_team_id,
                'away_team_id' => $fixture->away_team_id,
                'venue_id'     => $fixture->venue_id,
                'match_number' => $fixture->match_number,
                'match_date'   => $fixture->match_date->format('Y-m-d'),
                'warm_up_time' => $fixture->warm_up_time,
                'start_time'   => $fixture->start_time,
                'notes'        => $fixture->notes,
            ]);

        /** @var Fixture $fixture */
        $fixture2 = factory(Fixture::class)->make();

        // Brand new fixture without notes
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($fixture2->division_id, 'division_id')
            ->select($fixture2->home_team_id, 'home_team_id')
            ->select($fixture2->away_team_id, 'away_team_id')
            ->select($fixture2->venue_id, 'venue_id')
            ->type($fixture2->match_number, 'match_number')
            ->type($fixture2->match_date, 'match_date')
            ->type($fixture2->warm_up_time, 'warm_up_time')
            ->type($fixture2->start_time, 'start_time')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Fixture added!')
            ->seeInDatabase('fixtures', [
                'division_id'  => $fixture2->division_id,
                'home_team_id' => $fixture2->home_team_id,
                'away_team_id' => $fixture2->away_team_id,
                'venue_id'     => $fixture2->venue_id,
                'match_number' => $fixture2->match_number,
                'match_date'   => $fixture2->match_date->format('Y-m-d'),
                'warm_up_time' => $fixture2->warm_up_time,
                'start_time'   => $fixture2->start_time,
                'notes'        => '',
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
            ->seeInElement('.alert.alert-danger', 'The away team cannot be the same as the home team.')
            ->dontSeeInDatabase('fixtures', ['id' => 3]);

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
            ->seeInElement('.alert.alert-danger', 'The fixture for these two teams have already been added in this division.')
            ->dontSeeInDatabase('fixtures', ['id' => 3]);

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
            ->seeInElement('.alert.alert-danger', 'There is already a match with the same number in this division.')
            ->dontSeeInDatabase('fixtures', ['id' => 3]);
    }

    public function testEditFixture()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        // Don't change anything
        $this->seeInDatabase('fixtures', [
            'id'           => $fixture->id,
            'division_id'  => $fixture->division_id,
            'home_team_id' => $fixture->home_team_id,
            'away_team_id' => $fixture->away_team_id,
            'venue_id'     => $fixture->venue_id,
            'match_number' => $fixture->match_number,
            'match_date'   => $fixture->match_date->format('Y-m-d'),
            'warm_up_time' => $fixture->warm_up_time,
            'start_time'   => $fixture->start_time,
            'notes'        => $fixture->notes,
        ]);
        $this->visit(route(self::BASE_ROUTE . '.edit', [$fixture->id]))
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Fixture updated!')
            ->seeInDatabase('fixtures', [
                'id'           => $fixture->id,
                'division_id'  => $fixture->division_id,
                'home_team_id' => $fixture->home_team_id,
                'away_team_id' => $fixture->away_team_id,
                'venue_id'     => $fixture->venue_id,
                'match_number' => $fixture->match_number,
                'match_date'   => $fixture->match_date->format('Y-m-d'),
                'warm_up_time' => $fixture->warm_up_time,
                'start_time'   => $fixture->start_time,
                'notes'        => $fixture->notes,
            ]);

        /** @var Fixture $newFixture */
        $newFixture = factory(Fixture::class)->make(['id' => $fixture->id]);

        // Edit all  details
        $this->visit(route(self::BASE_ROUTE . '.edit', [$fixture->id]))
            ->select($newFixture->division_id, 'division_id')
            ->select($newFixture->home_team_id, 'home_team_id')
            ->select($newFixture->away_team_id, 'away_team_id')
            ->select($newFixture->venue_id, 'venue_id')
            ->type($newFixture->match_number, 'match_number')
            ->type($newFixture->match_date, 'match_date')
            ->type($newFixture->warm_up_time, 'warm_up_time')
            ->type($newFixture->start_time, 'start_time')
            ->type($newFixture->notes, 'notes')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Fixture updated!')
            ->seeInDatabase('fixtures', [
                'id'           => $fixture->id,
                'division_id'  => $newFixture->division_id,
                'home_team_id' => $newFixture->home_team_id,
                'away_team_id' => $newFixture->away_team_id,
                'venue_id'     => $newFixture->venue_id,
                'match_number' => $newFixture->match_number,
                'match_date'   => $newFixture->match_date->format('Y-m-d'),
                'warm_up_time' => $newFixture->warm_up_time,
                'start_time'   => $newFixture->start_time,
                'notes'        => $newFixture->notes,
            ]);
        $fixture = $newFixture;
        unset($newFixture);

        /** @var Fixture $newFixture */
        $newFixture = factory(Fixture::class)->make(['id' => $fixture->id]);

        // Remove the notes
        $this->visit(route(self::BASE_ROUTE . '.edit', [$fixture->id]))
            ->select($newFixture->division_id, 'division_id')
            ->select($newFixture->home_team_id, 'home_team_id')
            ->select($newFixture->away_team_id, 'away_team_id')
            ->select($newFixture->venue_id, 'venue_id')
            ->type($newFixture->match_number, 'match_number')
            ->type($newFixture->match_date, 'match_date')
            ->type($newFixture->warm_up_time, 'warm_up_time')
            ->type($newFixture->start_time, 'start_time')
            ->type('', 'notes')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Fixture updated!')
            ->seeInDatabase('fixtures', [
                'id'           => $fixture->id,
                'division_id'  => $newFixture->division_id,
                'home_team_id' => $newFixture->home_team_id,
                'away_team_id' => $newFixture->away_team_id,
                'venue_id'     => $newFixture->venue_id,
                'match_number' => $newFixture->match_number,
                'match_date'   => $newFixture->match_date->format('Y-m-d'),
                'warm_up_time' => $newFixture->warm_up_time,
                'start_time'   => $newFixture->start_time,
                'notes'        => '',
            ]);
        $fixture = $newFixture;
        unset($newFixture);

        // Use the same team for home and away
        $this->visit(route(self::BASE_ROUTE . '.edit', [$fixture->id]))
            ->select($fixture->home_team_id, 'home_team_id')
            ->select($fixture->home_team_id, 'away_team_id')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$fixture->id]))
            ->seeInElement('.alert.alert-danger', 'The away team cannot be the same as the home team.')
            ->seeInDatabase('fixtures', [
                'id'           => $fixture->id,
                'home_team_id' => $fixture->home_team_id,
                'away_team_id' => $fixture->away_team_id,
            ]);

        /** @var Fixture $newFixture */
        $newFixture = factory(Fixture::class)->create();

        // Use the same division, home and away teams of an existing fixture
        $this->visit(route(self::BASE_ROUTE . '.edit', [$fixture->id]))
            ->select($newFixture->division_id, 'division_id')
            ->select($newFixture->home_team_id, 'home_team_id')
            ->select($newFixture->away_team_id, 'away_team_id')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$fixture->id]))
            ->seeInElement('.alert.alert-danger', 'The fixture for these two teams have already been added in this division.')
            ->seeInDatabase('fixtures', [
                'id'           => $fixture->id,
                'division_id'  => $fixture->division_id,
                'home_team_id' => $fixture->home_team_id,
                'away_team_id' => $fixture->away_team_id,
            ]);

        // Use the same division and match number of an existing fixture
        $this->visit(route(self::BASE_ROUTE . '.edit', [$fixture->id]))
            ->select($newFixture->division_id, 'division_id')
            ->type($newFixture->match_number, 'match_number')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$fixture->id]))
            ->seeInElement('.alert.alert-danger', 'There is already a match with the same number in this division.')
            ->seeInDatabase('fixtures', [
                'id'           => $fixture->id,
                'division_id'  => $fixture->division_id,
                'match_number' => $fixture->match_number,
            ]);
    }

    public function testShowFixture()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $this->visit(route(self::BASE_ROUTE . '.show', [$fixture->id]))
            ->seeInElement('tbody tr td:nth-child(1)', $fixture->division->season)
            ->seeInElement('tbody tr td:nth-child(2)', $fixture->division->division)
            ->seeInElement('tbody tr td:nth-child(3)', $fixture->match_number)
            ->seeInElement('tbody tr td:nth-child(4)', $fixture->match_date->format('j M Y'))
            ->seeInElement('tbody tr td:nth-child(5)', $fixture->warm_up_time->format('H:i'))
            ->seeInElement('tbody tr td:nth-child(6)', $fixture->start_time->format('H:i'))
            ->seeInElement('tbody tr td:nth-child(7)', $fixture->home_team)
            ->seeInElement('tbody tr td:nth-child(8)', $fixture->away_team)
            ->seeInElement('tbody tr td:nth-child(9)', $fixture->venue)
            ->seeInElement('tbody tr td:nth-child(10)', $fixture->notes);
    }

    public function testDeleteFixture()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();
        $fixtureId = $fixture->id;

        $this->seeInDatabase('fixtures', [
            'id'           => $fixture->id,
            'division_id'  => $fixture->division_id,
            'home_team_id' => $fixture->home_team_id,
            'away_team_id' => $fixture->away_team_id,
            'venue_id'     => $fixture->venue_id,
            'match_number' => $fixture->match_number,
            'match_date'   => $fixture->match_date->format('Y-m-d'),
            'warm_up_time' => $fixture->warm_up_time,
            'start_time'   => $fixture->start_time,
            'notes'        => $fixture->notes,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$fixture->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Fixture deleted!')
            ->dontSeeInDatabase('fixtures', ['id' => $fixtureId]);

        // Delete fixture with existing appointment
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();
        /** @var AvailableAppointment $appointment */
        $appointment = factory(AvailableAppointment::class)->create(['fixture_id' => $fixture->id]);

        $this->seeInDatabase('fixtures', [
            'id'           => $fixture->id,
            'division_id'  => $fixture->division_id,
            'home_team_id' => $fixture->home_team_id,
            'away_team_id' => $fixture->away_team_id,
            'venue_id'     => $fixture->venue_id,
            'match_number' => $fixture->match_number,
            'match_date'   => $fixture->match_date->format('Y-m-d'),
            'warm_up_time' => $fixture->warm_up_time,
            'start_time'   => $fixture->start_time,
            'notes'        => $fixture->notes,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$fixture->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-danger', 'Cannot delete because they are existing appointments for this fixture.')
            ->seeInDatabase('fixtures', [
                'id'           => $fixture->id,
                'division_id'  => $fixture->division_id,
                'home_team_id' => $fixture->home_team_id,
                'away_team_id' => $fixture->away_team_id,
                'venue_id'     => $fixture->venue_id,
                'match_number' => $fixture->match_number,
                'match_date'   => $fixture->match_date->format('Y-m-d'),
                'warm_up_time' => $fixture->warm_up_time,
                'start_time'   => $fixture->start_time,
                'notes'        => $fixture->notes,
            ]);
    }
}