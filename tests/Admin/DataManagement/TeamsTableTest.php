<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use Tests\TestCase;
use App\Models\Team;
use App\Models\Club;

class TeamsTableTest extends TestCase
{
    const BASE_ROUTE = 'admin.data-management.teams';

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
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Teams');
    }

    public function testAddTeam()
    {
        $this->be($this->getFakeUser());

        /** @var Team $team */
        $team = factory(Team::class)->create();
        $teamId = $team->id;
        $clubId = $team->club_id;
        $teamName = $team->team;

        // Brand new team
        $newTeamName = 'New ' . $teamName;
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($clubId, 'club_id')
            ->type($newTeamName, 'team')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Team added!')
            ->seeInDatabase('teams', ['id' => $teamId + 1, 'club_id' => $clubId, 'team' => $newTeamName]);

        // Already existing team in the same club
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($clubId, 'club_id')
            ->type($teamName, 'team')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The team already exists in the same club.')
            ->seeInDatabase('teams', ['id' => $teamId, 'club_id' => $clubId, 'team' => $teamName]);
    }

    public function testEditTeam()
    {
        $this->be($this->getFakeUser());

        /** @var Team $team */
        $team = factory(Team::class)->create();
        $teamId = $team->id;
        $clubId = $team->club_id;
        $teamName = $team->team;

        // Change the name of the team
        $newTeamName = 'New ' . $teamName;
        $this->seeInDatabase('teams', ['id' => $teamId, 'club_id' => $clubId, 'team' => $teamName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$teamId]))
            ->type($newTeamName, 'team')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Team updated!')
            ->seeInDatabase('teams', ['id' => $teamId, 'club_id' => $clubId, 'team' => $newTeamName]);
        $teamName = $newTeamName;

        // Already existing team in the same club
        /** @var Team $anotherTeam */
        $anotherTeam = factory(Team::class)->create(['club_id' => $clubId]);
        $anotherTeamName = $anotherTeam->team;

        $this->seeInDatabase('teams', ['id' => $teamId, 'club_id' => $clubId, 'team' => $teamName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$teamId]))
            ->type($anotherTeamName, 'team')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$teamId]))
            ->seeInElement('.alert.alert-danger', 'The team already exists in the same club.')
            ->seeInDatabase('teams', ['id' => $teamId, 'club_id' => $clubId, 'team' => $teamName]);

        // Move team to a different club
        $anotherClub = factory(Club::class)->create();
        $anotherClubId = $anotherClub->id;

        $this->seeInDatabase('teams', ['id' => $teamId, 'club_id' => $clubId, 'team' => $teamName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$teamId]))
            ->select($anotherClubId, 'club_id')
            ->type($teamName, 'team')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Team updated!')
            ->seeInDatabase('teams', ['id' => $teamId, 'club_id' => $anotherClubId, 'team' => $newTeamName]);
    }

    public function testShowTeam()
    {
        $this->be($this->getFakeUser());

        /** @var Team $team */
        $team = factory(Team::class)->create();
        $teamId = $team->id;
        $clubId = $team->club_id;
        $teamName = $team->team;

        /** @var Club $club */
        $club = Club::find($clubId);
        $clubName = $club->club;

        $this->visit(route(self::BASE_ROUTE . '.show', [$teamId]))
            ->seeInElement('tbody tr td:nth-child(1)', $teamId)
            ->seeInElement('tbody tr td:nth-child(2)', $clubName)
            ->seeInElement('tbody tr td:nth-child(3)', $teamName);
    }

    public function testDeleteTeam()
    {
        $this->be($this->getFakeUser());

        /** @var Team $team */
        $team = factory(Team::class)->create();
        $teamId = $team->id;
        $clubId = $team->club_id;
        $teamName = $team->team;

        $this->seeInDatabase('teams', ['id' => $teamId, 'club_id' => $clubId, 'team' => $teamName])
            ->call('DELETE', route(self::BASE_ROUTE . '.destroy', [$teamId]))
            ->isRedirect(route(self::BASE_ROUTE . '.index'));
        $this->dontSeeInDatabase('teams', ['id' => $teamId]);
    }
}
