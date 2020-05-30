<?php

namespace Tests\Feature\CRUD;

use App\Events\TeamCreated;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function testAccessForGuests(): void
    {
        /** @var Team $team */
        $team = aTeam()->build();
        $clubId = $team->getClub()->getId();

        $this->get("/clubs/$clubId/teams")
            ->assertRedirect('/login');

        $this->get("/clubs/$clubId/teams/create")
            ->assertRedirect('/login');

        $this->post("/clubs/$clubId/teams")
            ->assertRedirect('/login');

        $this->get("/clubs/$clubId/teams/" . $team->getId() . '/edit')
            ->assertRedirect('/login');

        $this->put("/clubs/$clubId/teams/" . $team->getId())
            ->assertRedirect('/login');

        $this->delete("/clubs/$clubId/teams/" . $team->getId())
            ->assertRedirect('/login');
    }

    public function testAccessForUserWithoutThePermission(): void
    {
        /** @var Team $team */
        $team = aTeam()->build();
        $clubId = $team->getClub()->getId();

        $this->actingAs(factory(User::class)->create());

        $this->get("/clubs/$clubId/teams")
            ->assertForbidden();

        $this->get("/clubs/$clubId/teams/create")
            ->assertForbidden();

        $this->post("/clubs/$clubId/teams")
            ->assertForbidden();

        $this->get("/clubs/$clubId/teams/" . $team->getId() . '/edit')
            ->assertForbidden();

        $this->put("/clubs/$clubId/teams/" . $team->getId())
            ->assertForbidden();

        $this->delete("/clubs/$clubId/teams/" . $team->getId())
            ->assertForbidden();
    }

    public function testAccessForSuperAdmin(): void
    {
        /** @var Team $team */
        $team = aTeam()->buildWithoutSaving();
        $clubId = $team->getClub()->getId();

        $this->actingAs(factory(User::class)->create()->assignRole('Site Administrator'));

        $this->get("/clubs/$clubId/teams")
            ->assertOk();

        $this->get("/clubs/$clubId/teams/create")
            ->assertOk();

        $this->post("/clubs/$clubId/teams", $team->toArray())
            ->assertRedirect("/clubs/$clubId/teams");

        $team = Team::first();

        $this->get("/clubs/$clubId/teams/" . $team->getId() . '/edit')
            ->assertOk();

        $this->put("/clubs/$clubId/teams/" . $team->getId(), $team->toArray())
            ->assertRedirect("/clubs/$clubId/teams");

        $this->delete("/clubs/$clubId/teams/" . $team->getId())
            ->assertRedirect("/clubs/$clubId/teams");
    }

    public function testAccessForUnverifiedUsers(): void
    {
        /** @var Team $team */
        $team = aTeam()->build();
        $clubId = $team->getClub()->getId();

        $this->actingAs(factory(User::class)->state('unverified')->create());

        $this->get("/clubs/$clubId/teams")
            ->assertRedirect('/email/verify');

        $this->get("/clubs/$clubId/teams/create")
            ->assertRedirect('/email/verify');

        $this->post("/clubs/$clubId/teams")
            ->assertRedirect('/email/verify');

        $this->get("/clubs/$clubId/teams/" . $team->getId() . '/edit')
            ->assertRedirect('/email/verify');

        $this->put("/clubs/$clubId/teams/" . $team->getId())
            ->assertRedirect('/email/verify');

        $this->delete("/clubs/$clubId/teams/" . ($team->getId()))
            ->assertRedirect('/email/verify');
    }

    public function testAddingATeam(): void
    {
        /** @var Venue $sobellSC */
        $sobellSC = factory(Venue::class)->create(['name' => 'Sobell SC']);
        $clubId = aClub()->build()->getId();

        $this->actingAs(factory(User::class)->create()->givePermissionTo('view-seasons'));

        $this->post("/clubs/$clubId/teams", [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseMissing('teams', ['club_id' => $clubId, 'name' => 'London Scarlets']);

        $this->post("/clubs/$clubId/teams", ['club_id' => $clubId, 'name' => 'London Scarlets', 'venue_id' => null])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', [
            'club_id'  => $clubId,
            'name'     => 'London Scarlets',
            'venue_id' => null,
        ]);

        $this->post("/clubs/$clubId/teams", [
            'club_id'  => $clubId,
            'name'     => 'London Trollers',
            'venue_id' => $sobellSC->getId(),
        ])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', [
            'club_id'  => $clubId,
            'name'     => 'London Trollers',
            'venue_id' => $sobellSC->getId(),
        ]);

        $this->post("/clubs/$clubId/teams", ['club_id' => $clubId, 'name' => 'London Trollers', 'venue_id' => null])
            ->assertSessionHasErrors('name', 'The team already exists.');
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Trollers']);

        factory(Team::class)->create(['name' => 'Sydenham Giants']);
        $this->post("/clubs/$clubId/teams", ['club_id' => $clubId, 'name' => 'Sydenham Giants', 'venue_id' => null])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'Sydenham Giants']);
    }

    public function testEditingATeam(): void
    {
        /** @var Venue $sobellSC */
        $sobellSC = factory(Venue::class)->create(['name' => 'Sobell SC']);
        /** @var Team $team */
        $team = factory(Team::class)->create(['name' => 'London Scarlets', 'venue_id' => $sobellSC->getId()]);
        $clubId = $team->getClub()->getId();

        $this->actingAs(factory(User::class)->create()->givePermissionTo('view-seasons'));

        $this->put("/clubs/$clubId/teams/" . $team->getId(), [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Scarlets']);

        $this->put("/clubs/$clubId/teams/" . $team->getId(), ['name' => 'London Bees', 'venue_id' => null])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', [
            'club_id'  => $clubId,
            'name'     => 'London Bees',
            'venue_id' => null,
        ]);
        $this->assertDatabaseMissing('teams', [
            'club_id' => $clubId,
            'name'    => 'London Scarlets',
        ]);

        $this->put("/clubs/$clubId/teams/" . $team->getId(),
            ['name' => 'London Bees', 'venue_id' => $sobellSC->getId()])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', [
            'club_id'  => $clubId,
            'name'     => 'London Bees',
            'venue_id' => $sobellSC->getId(),
        ]);

        factory(Team::class)->create([
            'club_id' => $clubId,
            'name'    => 'The Patriots',
        ]);

        $this->put("/clubs/$clubId/teams/" . $team->getId(), ['name' => 'The Patriots', 'venue_id' => null])
            ->assertSessionHasErrors('name', 'The team already exists in this club.');
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Bees']);

        $globalWarriors = aClub()->withName('Global Warriors')->build();
        factory(Team::class)->create(['club_id' => $globalWarriors->getId(), 'name' => 'London Warriors']);

        $this->put("/clubs/$clubId/teams/" . $team->getId(), ['name' => 'London Warriors', 'venue_id' => null])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Warriors']);
        $this->assertDatabaseHas('teams', ['club_id' => $globalWarriors->getId(), 'name' => 'London Warriors']);
    }

    public function testDeletingATeam(): void
    {
        /** @var Team $team */
        $team = factory(Team::class)->create(['name' => 'London Scarlets']);
        $clubId = $team->getClub()->getId();

        $this->actingAs(factory(User::class)->create()->givePermissionTo('view-seasons'));

        $this->delete("/clubs/$clubId/teams/" . $team->getId())
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('teams', ['name' => 'London Scarlets']);

        $this->delete("/clubs/$clubId/teams/" . ($team->getId() + 1))
            ->assertNotFound();
    }

    public function testAddingTeamWillDispatchTheEvent(): void
    {
        Event::fake();

        // Cannot create a venue as the events are faked and the Venue model
        // needs to create a UUID
        $clubId = aClub()->withoutVenue()->build()->getId();
        $this->actingAs(factory(User::class)->create()->givePermissionTo('view-seasons'));

        $this->post("/clubs/$clubId/teams", ['club_id' => $clubId, 'name' => 'London Scarlets', 'venue_id' => null]);

        Event::assertDispatched(TeamCreated::class);
    }
}
