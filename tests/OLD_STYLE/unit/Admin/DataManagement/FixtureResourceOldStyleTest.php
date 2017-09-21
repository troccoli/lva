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
use Tests\OldStyleTestCase;
use LVA\Models\Fixture;

class FixtureResourceOldStyleTest extends OldStyleTestCase
{
    const BASE_ROUTE = 'fixtures';

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
            'warm_up_time' => $fixture->warm_up_time->format('H:i:00'),
            'start_time'   => $fixture->start_time->format('H:i:00'),
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
            'warm_up_time' => $fixture->warm_up_time->format('H:i:00'),
            'start_time'   => $fixture->start_time->format('H:i:00'),
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
                'warm_up_time' => $fixture->warm_up_time->format('H:i:00'),
                'start_time'   => $fixture->start_time->format('H:i:00'),
                'notes'        => $fixture->notes,
            ]);
    }
}
