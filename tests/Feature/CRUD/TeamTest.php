<?php

namespace Tests\Feature\CRUD;

use App\Events\TeamCreated;
use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TeamTest extends TestCase
{
    public function testAccessForGuests(): void
    {
        $team = Team::factory()->create();
        $clubId = $team->getClub()->getId();

        $this->get("/clubs/$clubId/teams")
             ->assertRedirect('/login');

        $this->get("/clubs/$clubId/teams/create")
             ->assertRedirect('/login');

        $this->post("/clubs/$clubId/teams")
             ->assertRedirect('/login');

        $this->get("/clubs/$clubId/teams/{$team->getId()}/edit")
             ->assertRedirect('/login');

        $this->put("/clubs/$clubId/teams/{$team->getId()}")
             ->assertRedirect('/login');

        $this->delete("/clubs/$clubId/teams/{$team->getId()}")
             ->assertRedirect('/login');
    }

    public function testAccessForUsersWithoutAnyCorrectRoles(): void
    {
        $team = Team::factory()->create();
        $clubId = $team->getClub()->getId();

        $this->be(User::factory()->create());

        $this->get("/clubs/$clubId/teams")
             ->assertForbidden();

        $this->get("/clubs/$clubId/teams/create")
             ->assertForbidden();

        $this->post("/clubs/$clubId/teams")
             ->assertForbidden();

        $this->get("/clubs/$clubId/teams/{$team->getId()}/edit")
             ->assertForbidden();

        $this->put("/clubs/$clubId/teams/{$team->getId()}")
             ->assertForbidden();

        $this->delete("/clubs/$clubId/teams/{$team->getId()}")
             ->assertForbidden();
    }

    public function testAccessForSiteAdministrators(): void
    {
        $team = Team::factory()->make();
        $clubId = $team->getClub()->getId();

        $this->be(User::factory()->create()->assignRole('Site Administrator'));

        $this->get("/clubs/$clubId/teams")
             ->assertOk();

        $this->get("/clubs/$clubId/teams/create")
             ->assertOk();

        $this->post("/clubs/$clubId/teams", $team->toArray())
             ->assertRedirect("/clubs/$clubId/teams");

        $team = Team::first();

        $this->get("/clubs/$clubId/teams/{$team->getId()}/edit")
             ->assertOk();

        $this->put("/clubs/$clubId/teams/{$team->getId()}", $team->toArray())
             ->assertRedirect("/clubs/$clubId/teams");

        $this->delete("/clubs/$clubId/teams/{$team->getId()}")
             ->assertRedirect("/clubs/$clubId/teams");
    }

    public function testAccessForUnverifiedUsers(): void
    {
        $team = Team::factory()->create();
        $clubId = $team->getClub()->getId();

        $this->be(User::factory()->unverified()->create());

        $this->get("/clubs/$clubId/teams")
             ->assertRedirect('/email/verify');

        $this->get("/clubs/$clubId/teams/create")
             ->assertRedirect('/email/verify');

        $this->post("/clubs/$clubId/teams")
             ->assertRedirect('/email/verify');

        $this->get("/clubs/$clubId/teams/{$team->getId()}/edit")
             ->assertRedirect('/email/verify');

        $this->put("/clubs/$clubId/teams/{$team->getId()}")
             ->assertRedirect('/email/verify');

        $this->delete("/clubs/$clubId/teams/{$team->getId()}")
             ->assertRedirect('/email/verify');
    }

    public function testAccessForClubAdministrators(): void
    {
        /** @var Club $club */
        $club = Club::factory()->create();
        $team = Team::factory()->for($club)->make();

        $this->be(User::factory()->create()->assignRole(RolesHelper::clubSecretary($club)));

        $this->get("/clubs/{$club->getId()}/teams")
             ->assertOk();

        $this->get("/clubs/{$club->getId()}/teams/create")
             ->assertOk();

        $this->post("/clubs/{$club->getId()}/teams", $team->toArray())
             ->assertRedirect("/clubs/{$club->getId()}/teams");

        $team = Team::first();

        $this->get("/clubs/{$club->getId()}/teams/{$team->getId()}/edit")
             ->assertOk();

        $this->put("/clubs/{$club->getId()}/teams/{$team->getId()}", $team->toArray())
             ->assertRedirect("/clubs/{$club->getId()}/teams");

        $this->delete("/clubs/{$club->getId()}/teams/{$team->getId()}")
             ->assertRedirect("/clubs/{$club->getId()}/teams");
    }

    public function testAccessForTeamAdministrators(): void
    {
        $club = Club::factory()->create();
        /** @var Team $team */
        $team = Team::factory()->for($club)->create();

        $this->be(User::factory()->create()->assignRole(RolesHelper::teamSecretary($team)));

        $this->get("/clubs/{$club->getId()}/teams")
             ->assertOk();

        $this->get("/clubs/{$club->getId()}/teams/create")
             ->assertForbidden();

        $this->post("/clubs/{$club->getId()}/teams", $team->toArray())
             ->assertForbidden();

        $this->get("/clubs/{$club->getId()}/teams/{$team->getId()}/edit")
             ->assertOk();

        $this->put("/clubs/{$club->getId()}/teams/{$team->getId()}", $team->toArray())
             ->assertRedirect("/clubs/{$club->getId()}/teams");

        $this->delete("/clubs/{$club->getId()}/teams/{$team->getId()}")
             ->assertForbidden();

        $anotherTeam = Team::factory()->for($club)->create();

        $this->get("/clubs/{$club->getId()}/teams/{$anotherTeam->getId()}/edit")
             ->assertForbidden();

        $this->put("/clubs/{$club->getId()}/teams/{$anotherTeam->getId()}", $team->toArray())
             ->assertForbidden();

        $this->delete("/clubs/{$club->getId()}/teams/{$team->getId()}")
             ->assertForbidden();

        $anotherClub = Club::factory()->create();

        $yetAnotherTeam = Team::factory()->for($anotherClub)->create();

        $this->get("/clubs/{$anotherClub->getId()}/teams")
             ->assertForbidden();

        $this->get("/clubs/{$anotherClub->getId()}/teams/create")
             ->assertForbidden();

        $this->post("/clubs/{$anotherClub->getId()}/teams", $team->toArray())
             ->assertForbidden();

        $this->get("/clubs/{$anotherClub->getId()}/teams/{$yetAnotherTeam->getId()}/edit")
             ->assertForbidden();

        $this->put("/clubs/{$anotherClub->getId()}/teams/{$yetAnotherTeam->getId()}", $team->toArray())
             ->assertForbidden();

        $this->delete("/clubs/{$anotherClub->getId()}/teams/{$yetAnotherTeam->getId()}")
             ->assertForbidden();
    }

    public function testAddingATeam(): void
    {
        $venue = Venue::factory()->create(['name' => 'Sobell SC']);
        $club = Club::factory()->create();

        $this->be($this->siteAdmin);

        $this->post("/clubs/{$club->getId()}/teams", [])
             ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseMissing('teams', ['club_id' => $club->getId(), 'name' => 'London Scarlets']);

        $this->post(
            "/clubs/{$club->getId()}/teams",
            ['club_id' => $club->getId(), 'name' => 'London Scarlets', 'venue_id' => null]
        )
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'teams',
            [
                'club_id' => $club->getId(),
                'name' => 'London Scarlets',
                'venue_id' => null,
            ]
        );

        $this->post(
            "/clubs/{$club->getId()}/teams",
            [
                'club_id' => $club->getId(),
                'name' => 'London Trollers',
                'venue_id' => $venue->getId(),
            ]
        )
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'teams',
            [
                'club_id' => $club->getId(),
                'name' => 'London Trollers',
                'venue_id' => $venue->getId(),
            ]
        );

        $this->post(
            "/clubs/{$club->getId()}/teams",
            ['club_id' => $club->getId(), 'name' => 'London Trollers', 'venue_id' => null]
        )
             ->assertSessionHasErrors('name', 'The team already exists.');
        $this->assertDatabaseHas('teams', ['club_id' => $club->getId(), 'name' => 'London Trollers']);

        Team::factory()->create(['name' => 'Sydenham Giants']);
        $this->post(
            "/clubs/{$club->getId()}/teams",
            ['club_id' => $club->getId(), 'name' => 'Sydenham Giants', 'venue_id' => null]
        )
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', ['club_id' => $club->getId(), 'name' => 'Sydenham Giants']);
    }

    public function testEditingATeam(): void
    {
        $venue = Venue::factory()->create(['name' => 'Sobell SC']);
        $team = Team::factory()->for($venue)->create(['name' => 'London Scarlets']);
        $club = $team->getClub();

        $this->be($this->siteAdmin);

        $this->put("/clubs/{$club->getId()}/teams/{$team->getId()}", [])
             ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseHas('teams', ['club_id' => $club->getId(), 'name' => 'London Scarlets']);

        $this->put("/clubs/{$club->getId()}/teams/{$team->getId()}", ['name' => 'London Bees', 'venue_id' => null])
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'teams',
            [
                'club_id' => $club->getId(),
                'name' => 'London Bees',
                'venue_id' => null,
            ]
        );
        $this->assertDatabaseMissing(
            'teams',
            [
                'club_id' => $club->getId(),
                'name' => 'London Scarlets',
            ]
        );

        $this->put(
            "/clubs/{$club->getId()}/teams/{$team->getId()}",
            ['name' => 'London Bees', 'venue_id' => $venue->getId()]
        )
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'teams',
            [
                'club_id' => $club->getId(),
                'name' => 'London Bees',
                'venue_id' => $venue->getId(),
            ]
        );

        Team::factory()->for($club)->create(['name' => 'The Patriots']);

        $this->put("/clubs/{$club->getId()}/teams/{$team->getId()}", ['name' => 'The Patriots', 'venue_id' => null])
             ->assertSessionHasErrors('name', 'The team already exists in this club.');
        $this->assertDatabaseHas('teams', ['club_id' => $club->getId(), 'name' => 'London Bees']);

        $globalWarriors = Club::factory()->create(['name' => 'Global Warriors']);
        Team::factory()->for($globalWarriors)->create(['name' => 'London Warriors']);

        $this->put("/clubs/{$club->getId()}/teams/{$team->getId()}", ['name' => 'London Warriors', 'venue_id' => null])
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', ['club_id' => $club->getId(), 'name' => 'London Warriors']);
        $this->assertDatabaseHas('teams', ['club_id' => $globalWarriors->getId(), 'name' => 'London Warriors']);
    }

    public function testDeletingATeam(): void
    {
        $team = Team::factory()->create(['name' => 'London Scarlets']);
        $club = $team->getClub();

        $this->be($this->siteAdmin);

        $this->delete("/clubs/{$club->getId()}/teams/{$team->getId()}")
             ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('teams', ['name' => 'London Scarlets']);

        $this->delete("/clubs/{$club->getId()}/teams/".($team->getId() + 1))
             ->assertNotFound();
    }

    public function testAddingTeamWillDispatchTheEvent(): void
    {
        Event::fake();

        // Cannot create a venue as the events are faked and the Venue model
        // needs to create a UUID
        $club = Club::factory()->withoutVenue()->create();
        $this->actingAs($this->siteAdmin)
             ->post(
                 "/clubs/{$club->getId()}/teams",
                 ['club_id' => $club->getId(), 'name' => 'London Scarlets', 'venue_id' => null]
             );

        Event::assertDispatched(TeamCreated::class);
    }
}
