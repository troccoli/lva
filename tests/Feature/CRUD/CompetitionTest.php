<?php

namespace Tests\Feature\CRUD;

use App\Models\Competition;
use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionTest extends TestCase
{
    use RefreshDatabase;

    public function testAccessForGuests(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();
        $seasonId = $competition->getSeason()->getId();

        $this->get("/seasons/$seasonId/competitions")
            ->assertRedirect('/login');

        $this->get("/seasons/$seasonId/competitions/create")
            ->assertRedirect('/login');

        $this->post("/seasons/$seasonId/competitions")
            ->assertRedirect('/login');

        $this->get("/seasons/$seasonId/competitions/" . $competition->getId() . '/edit')
            ->assertRedirect('/login');

        $this->put("/seasons/$seasonId/competitions/" . $competition->getId())
            ->assertRedirect('/login');

        $this->delete("/seasons/$seasonId/competitions/" . $competition->getId())
            ->assertRedirect('/login');
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->make();
        $seasonId = $competition->getSeason()->getId();

        $this->actingAs(factory(User::class)->create());

        $this->get("/seasons/$seasonId/competitions")
            ->assertOk();

        $this->get("/seasons/$seasonId/competitions/create")
            ->assertOk();

        $this->post("/seasons/$seasonId/competitions", $competition->toArray())
            ->assertRedirect("/seasons/$seasonId/competitions");

        $competition = Competition::first();

        $this->get("/seasons/$seasonId/competitions/" . $competition->getId() . '/edit')
            ->assertOk();

        $this->put("/seasons/$seasonId/competitions/" . $competition->getId(), $competition->toArray())
            ->assertRedirect("/seasons/$seasonId/competitions");

        $this->delete("/seasons/$seasonId/competitions/" . $competition->getId())
            ->assertRedirect("/seasons/$seasonId/competitions");
    }

    public function testAccessForUnverifiedUsers(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();
        $seasonId = $competition->getSeason()->getId();

        $this->actingAs(factory(User::class)->state('unverified')->create());

        $this->get("/seasons/$seasonId/competitions")
            ->assertRedirect('/email/verify');

        $this->get("/seasons/$seasonId/competitions/create")
            ->assertRedirect('/email/verify');

        $this->post("/seasons/$seasonId/competitions")
            ->assertRedirect('/email/verify');

        $this->get("/seasons/$seasonId/competitions/" . $competition->getId() . '/edit')
            ->assertRedirect('/email/verify');

        $this->put("/seasons/$seasonId/competitions/" . $competition->getId())
            ->assertRedirect('/email/verify');

        $this->delete("/seasons/$seasonId/competitions/" . ($competition->getId()))
            ->assertRedirect('/email/verify');
    }

    public function testAddingACompetition(): void
    {
        $seasonId = factory(Season::class)->create()->getId();

        $this->actingAs(factory(User::class)->create());

        // Missing required fields
        $this->post("/seasons/$seasonId/competitions", [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseMissing('competitions', ['season_id' => $seasonId, 'name' => 'London League - Men']);

        // OK
        $this->post("/seasons/$seasonId/competitions", ['name' => 'London League - Men'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competitions', ['season_id' => $seasonId, 'name' => 'London League - Men']);

        // Existing competition
        $this->post("/seasons/$seasonId/competitions", ['name' => 'London League - Men'])
            ->assertSessionHasErrors('name', 'The competition already exists.');
        $this->assertDatabaseHas('competitions', ['season_id' => $seasonId, 'name' => 'London League - Men']);

        // Same competition in different season
        factory(Competition::class)->create(['name' => 'Youth Games']);
        $this->post("/seasons/$seasonId/competitions", ['name' => 'Youth Games'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competitions', ['season_id' => $seasonId, 'name' => 'Youth Games']);
    }

    public function testEditingACompetition(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create(['name' => 'London League - Men']);
        $seasonId = $competition->getSeason()->getId();

        $this->actingAs(factory(User::class)->create());

        // Missing required fields
        $this->put("/seasons/$seasonId/competitions/" . $competition->getId(), [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseHas('competitions', ['season_id' => $seasonId, 'name' => 'London League - Men']);

        // OK
        $this->put("/seasons/$seasonId/competitions/" . $competition->getId(), ['name' => 'London League - Women'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competitions', ['season_id' => $seasonId, 'name' => 'London League - Women']);
        $this->assertDatabaseMissing('competitions', ['season_id' => $seasonId, 'name' => 'London League - Men']);

        // Existing competition
        factory(Competition::class)->create([
            'season_id' => $seasonId,
            'name'      => 'University League',
        ]);
        $this->put("/seasons/$seasonId/competitions/" . $competition->getId(), ['name' => 'University League'])
            ->assertSessionHasErrors('name', 'The competition already exists in this season.');
        $this->assertDatabaseHas('competitions', ['season_id' => $seasonId, 'name' => 'London League - Women']);

        // Same competition in different season
        $youthGames = factory(Competition::class)->create(['name' => 'Youth Games']);
        $this->put("/seasons/$seasonId/competitions/" . $competition->getId(), ['name' => 'Youth Games'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competitions', ['season_id' => $seasonId, 'name' => 'Youth Games']);
        $this->assertDatabaseHas('competitions', ['season_id' => $youthGames->getSeason()->getId(), 'name' => 'Youth Games']);
    }

    public function testDeletingACompetition(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();
        $seasonId = $competition->getSeason()->getId();

        $this->actingAs(factory(User::class)->create());

        $this->delete("/seasons/$seasonId/competitions/" . $competition->getId())
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('competitions', ['id' => $competition->getId()]);

        $this->delete("/seasons/$seasonId/competitions/" . $competition->getId())
            ->assertNotFound();
    }
}
