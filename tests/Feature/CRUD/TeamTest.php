<?php

namespace Tests\Feature\CRUD;

use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function testAccessForAuthenticatedUsers(): void
    {
        /** @var Team $team */
        $team = aTeam()->buildWithoutSaving();
        $clubId = $team->getClub()->getId();

        $this->actingAs(factory(User::class)->create());

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
        $venueId = factory(Venue::class)->create()->getId();
        $clubId = aClub()->build()->getId();

        $this->actingAs(factory(User::class)->create());

        // Missing required fields
        $this->post("/clubs/$clubId/teams", [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseMissing('teams', ['club_id' => $clubId, 'name' => 'London Scarlets']);

        // OK
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
            'venue_id' => $venueId,
        ])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', [
            'club_id'  => $clubId,
            'name'     => 'London Trollers',
            'venue_id' => $venueId,
        ]);

        // Already existing team
        $this->post("/clubs/$clubId/teams", ['club_id' => $clubId, 'name' => 'London Trollers', 'venue_id' => null])
            ->assertSessionHasErrors('name', 'The team already exists.');
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Trollers']);

        // Existing team in another club
        factory(Team::class)->create(['name' => 'Sydenham Giants']);
        $this->post("/clubs/$clubId/teams", ['club_id' => $clubId, 'name' => 'Sydenham Giants', 'venue_id' => null])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'Sydenham Giants']);
    }

    public function testEditingATeam(): void
    {
        $venueId = factory(Venue::class)->create()->getId();
        /** @var Team $team */
        $team = factory(Team::class)->create(['name' => 'London Scarlets', 'venue_id' => $venueId]);
        $clubId = $team->getClub()->getId();

        $this->actingAs(factory(User::class)->create());

        // Missing required fields
        $this->put("/clubs/$clubId/teams/" . $team->getId(), [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Scarlets']);

        // OK - change name
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

        // OK - change venue
        $this->put("/clubs/$clubId/teams/" . $team->getId(), ['name' => 'London Bees', 'venue_id' => $venueId])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', [
            'club_id'  => $clubId,
            'name'     => 'London Bees',
            'venue_id' => $venueId,
        ]);

        // Already existing team in the club
        factory(Team::class)->create([
            'club_id' => $clubId,
            'name'    => 'The Patriots',
        ]);
        $this->put("/clubs/$clubId/teams/" . $team->getId(), ['name' => 'The Patriots', 'venue_id' => null])
            ->assertSessionHasErrors('name', 'The team already exists in this club.');
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Bees']);

        // Existing team in another club
        $anotherClub = aClub()->build();
        factory(Team::class)->create(['club_id' => $anotherClub->getId(), 'name' => 'London Warriors']);

        $this->put("/clubs/$clubId/teams/" . $team->getId(), ['name' => 'London Warriors', 'venue_id' => null])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Warriors']);
        $this->assertDatabaseHas('teams', ['club_id' => $anotherClub->getId(), 'name' => 'London Warriors']);
    }

    public function testDeletingATeam(): void
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();
        $clubId = $team->getClub()->getId();

        $this->actingAs(factory(User::class)->create());

        $this->delete("/clubs/$clubId/teams/" . $team->getId())
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('teams', ['id' => $team->getId()]);

        $this->delete("/clubs/$clubId/teams/" . $team->getId())
            ->assertNotFound();
    }
}
