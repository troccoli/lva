<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use LVA\Models\Fixture;
use LVA\User;
use Tests\TestCase;
use LVA\Models\Team;
use LVA\Models\Club;

class TeamResourceTest extends TestCase
{
    const BASE_ROUTE = 'teams';

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
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Teams');
    }

    public function testAddTeam()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Team $team */
        $team = factory(Team::class)->make();

        // Brand new team
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($team->club_id, 'club_id')
            ->type($team->team, 'team')
            ->type($team->trigram, 'trigram')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Team added!')
            ->seeInDatabase('teams', [
                'club_id' => $team->club_id,
                'team'    => $team->team,
                'trigram' => $team->trigram,
            ]);

        // Already existing team in the same club
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($team->club_id, 'club_id')
            ->type($team->team, 'team')
            ->type($team->trigram, 'trigram')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The team already exists in the same club.')
            ->seeInDatabase('teams', [
                'club_id' => $team->club_id,
                'team'    => $team->team,
                'trigram' => $team->trigram,
            ]);

        // Already assigned trigram
        /** @var Team $team */
        $newTeam = factory(Team::class)->make();
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($newTeam->club_id, 'club_id')
            ->type($newTeam->team, 'team')
            ->type($team->trigram, 'trigram')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The trigram has already been taken.');

        // Non numeric nor string trigram
        $trigram = $this->faker->regexify('[!@£$%&*()_+=-;,./?><":{}]{3}');
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($newTeam->club_id, 'club_id')
            ->type($newTeam->team, 'team')
            ->type($trigram, 'trigram')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The trigram may only contain letters and numbers.');

        // Too short trigram
        $trigram = $this->faker->regexify('[A-Z]{2}');
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($newTeam->club_id, 'club_id')
            ->type($newTeam->team, 'team')
            ->type($trigram, 'trigram')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The trigram must be 3 characters.');

        // Too long trigram
        $trigram = $this->faker->regexify('[A-Z]{4}');
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($newTeam->club_id, 'club_id')
            ->type($newTeam->team, 'team')
            ->type($trigram, 'trigram')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The trigram must be 3 characters.');
    }

    public function testEditTeam()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        // Don't change anything
        $this->seeInDatabase('teams', [
            'id'      => $team->id,
            'club_id' => $team->club_id,
            'team'    => $team->team,
            'trigram' => $team->trigram,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Team updated!')
            ->seeInDatabase('teams', [
                'id'      => $team->id,
                'club_id' => $team->club_id,
                'team'    => $team->team,
                'trigram' => $team->trigram,
            ]);

        /** @var Team $newTeam */
        $newTeam = factory(Team::class)->make();

        // Change the name of the team
        $this->seeInDatabase('teams', [
            'id'      => $team->id,
            'club_id' => $team->club_id,
            'team'    => $team->team,
            'trigram' => $team->trigram,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->type($newTeam->team, 'team')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Team updated!')
            ->seeInDatabase('teams', [
                'id'      => $team->id,
                'club_id' => $team->club_id,
                'team'    => $newTeam->team,
                'trigram' => $team->trigram,
            ]);
        $team->team = $newTeam->team;

        /** @var Team $newTeam */
        $newTeam = factory(Team::class)->make();

        // Change the trigram of the team
        $this->seeInDatabase('teams', [
            'id'      => $team->id,
            'club_id' => $team->club_id,
            'team'    => $team->team,
            'trigram' => $team->trigram,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->type($newTeam->trigram, 'trigram')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Team updated!')
            ->seeInDatabase('teams', [
                'id'      => $team->id,
                'club_id' => $team->club_id,
                'team'    => $team->team,
                'trigram' => $newTeam->trigram,
            ]);
        $team->trigram = $newTeam->trigram;
        unset($newTeam);

        // Already existing team in the same club
        /** @var Team $newTeam */
        $newTeam = factory(Team::class)->create(['club_id' => $team->club_id]);

        $this->seeInDatabase('teams', [
            'id'      => $team->id,
            'club_id' => $team->club_id,
            'team'    => $team->team,
            'trigram' => $team->trigram,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->type($newTeam->team, 'team')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->seeInElement('.alert.alert-danger', 'The team already exists in the same club.')
            ->seeInDatabase('teams', [
                'id'      => $team->id,
                'club_id' => $team->club_id,
                'team'    => $team->team,
                'trigram' => $team->trigram,
            ]);

        // Move team to a different club
        $club = factory(Club::class)->create();

        $this->seeInDatabase('teams', [
            'id'      => $team->id,
            'club_id' => $team->club_id,
            'team'    => $team->team,
            'trigram' => $team->trigram,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->select($club->id, 'club_id')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Team updated!')
            ->seeInDatabase('teams', [
                'id'      => $team->id,
                'club_id' => $club->id,
                'team'    => $team->team,
                'trigram' => $team->trigram,
            ]);
        $team->club_id = $club->id;

        // Already assigned trigram
        /** @var Team $newTeam */
        $newTeam = factory(Team::class)->create();

        $this->seeInDatabase('teams', [
            'id'      => $team->id,
            'club_id' => $team->club_id,
            'team'    => $team->team,
            'trigram' => $team->trigram,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->type($newTeam->trigram, 'trigram')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->seeInElement('.alert.alert-danger', 'The trigram has already been taken.')
            ->seeInDatabase('teams', [
                'id'      => $team->id,
                'club_id' => $team->club_id,
                'team'    => $team->team,
                'trigram' => $team->trigram,
            ]);

        // Non numeric nor string trigram
        $trigram = $this->faker->regexify('[,.><;":\|{}±!@£$%^&*()-=_+]{3}');
        $this->visit(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->type($trigram, 'trigram')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->seeInElement('.alert.alert-danger', 'The trigram may only contain letters and numbers.')
            ->seeInDatabase('teams', [
                'id'      => $team->id,
                'club_id' => $team->club_id,
                'team'    => $team->team,
                'trigram' => $team->trigram,
            ]);

        // Too short trigram
        $trigram = $this->faker->regexify('[A-Z]{2}');
        $this->visit(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->type($trigram, 'trigram')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->seeInElement('.alert.alert-danger', 'The trigram must be 3 characters.')
            ->seeInDatabase('teams', [
                'id'      => $team->id,
                'club_id' => $team->club_id,
                'team'    => $team->team,
                'trigram' => $team->trigram,
            ]);

        // Too long trigram
        $trigram = $this->faker->regexify('[A-Z]{4}');
        $this->visit(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->type($trigram, 'trigram')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$team->id]))
            ->seeInElement('.alert.alert-danger', 'The trigram must be 3 characters.')
            ->seeInDatabase('teams', [
                'id'      => $team->id,
                'club_id' => $team->club_id,
                'team'    => $team->team,
                'trigram' => $team->trigram,
            ]);
    }

    public function testShowTeam()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Team $team */
        $team = factory(Team::class)->create();

        $this->visit(route(self::BASE_ROUTE . '.show', [$team->id]))
            ->seeInElement('tbody tr td:nth-child(1)', $team->id)
            ->seeInElement('tbody tr td:nth-child(2)', $team->club)
            ->seeInElement('tbody tr td:nth-child(3)', $team->team)
            ->SeeInElement('tbody tr td:nth-child(4)', $team->trigram);
    }

    public function testDeleteTeam()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Team $team */
        $team = factory(Team::class)->create();
        $teamId = $team->id;

        $this->seeInDatabase('teams', [
            'id'      => $team->id,
            'club_id' => $team->club_id,
            'team'    => $team->team,
            'trigram' => $team->trigram,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$team->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Team deleted!')
            ->dontSeeInDatabase('teams', ['id' => $teamId]);

        // Delete team with fixtures
        /** @var Team $team */
        $team = factory(Team::class)->create();
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create(['home_team_id' => $team->id]);

        $this->seeInDatabase('teams', [
            'id'      => $team->id,
            'club_id' => $team->club_id,
            'team'    => $team->team,
            'trigram' => $team->trigram,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$team->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-danger', 'Cannot delete because they are existing fixtures for this team.')
            ->seeInDatabase('teams', [
                'id'      => $team->id,
                'club_id' => $team->club_id,
                'team'    => $team->team,
                'trigram' => $team->trigram,
            ]);

        Fixture::destroy($fixture->id);
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create(['away_team_id' => $team->id]);

        $this->seeInDatabase('teams', [
            'id'      => $team->id,
            'club_id' => $team->club_id,
            'team'    => $team->team,
            'trigram' => $team->trigram,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$team->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-danger', 'Cannot delete because they are existing fixtures for this team.')
            ->seeInDatabase('teams', [
                'id'      => $team->id,
                'club_id' => $team->club_id,
                'team'    => $team->team,
                'trigram' => $team->trigram,
            ]);
    }
}
