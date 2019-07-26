<?php

namespace Tests\Feature\CRUD;

use App\Models\Team;
use App\Models\User;
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
        $clubId = aClub()->build()->getId();

        $this->actingAs(factory(User::class)->create());

        $this->post("/clubs/$clubId/teams", [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseMissing('teams', ['club_id' => $clubId, 'name' => 'London Scarlets']);

        $this->post("/clubs/$clubId/teams", ['club_id' => $clubId, 'name' => 'London Scarlets'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Scarlets']);

        $this->post("/clubs/$clubId/teams", ['club_id' => $clubId, 'name' => 'London Scarlets'])
            ->assertSessionHasErrors('name', 'The team already exists.');
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Scarlets']);

        factory(Team::class)->create(['name' => 'Sydenham Giants']);
        $this->post("/clubs/$clubId/teams", ['club_id' => $clubId, 'name' => 'Sydenham Giants'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'Sydenham Giants']);
    }

    public function testEditingATeam(): void
    {
        /** @var Team $team */
        $team = factory(Team::class)->create(['name' => 'London Scarlets']);
        $clubId = $team->getClub()->getId();

        $this->actingAs(factory(User::class)->create());

        $this->put("/clubs/$clubId/teams/" . $team->getId(), [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Scarlets']);

        $this->put("/clubs/$clubId/teams/" . $team->getId(), ['name' => 'London Bees'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Bees']);
        $this->assertDatabaseMissing('teams', ['club_id' => $clubId, 'name' => 'London Scarlets']);

        factory(Team::class)->create([
            'club_id' => $clubId,
            'name'      => 'The Patriots',
        ]);

        $this->put("/clubs/$clubId/teams/" . $team->getId(), ['name' => 'The Patriots'])
            ->assertSessionHasErrors('name', 'The team already exists in this club.');
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'London Bees']);

        $youthGames = factory(Team::class)->create(['name' => 'Sydenham Giants']);

        $this->put("/clubs/$clubId/teams/" . $team->getId(), ['name' => 'Sydenham Giants'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('teams', ['club_id' => $clubId, 'name' => 'Sydenham Giants']);
        $this->assertDatabaseHas('teams', ['club_id' => $youthGames->getClub()->getId(), 'name' => 'Sydenham Giants']);
    }

    public function testDeletingATeam(): void
    {
        /** @var Team $team */
        $team = factory(Team::class)->create(['name' => 'London Scarlets']);
        $clubId = $team->getClub()->getId();

        $this->actingAs(factory(User::class)->create());

        $this->delete("/clubs/$clubId/teams/" . $team->getId())
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('teams', ['name' => 'London Scarlets']);

        $this->delete("/clubs/$clubId/teams/" . ($team->getId() + 1))
            ->assertNotFound();
    }
}
