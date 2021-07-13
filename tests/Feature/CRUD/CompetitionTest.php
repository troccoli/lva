<?php

namespace Tests\Feature\CRUD;

use App\Events\CompetitionCreated;
use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Season;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CompetitionTest extends TestCase
{
    public function testAccessForGuests(): void
    {
        $competition = Competition::factory()->create();
        $seasonId = $competition->getSeason()->getId();

        $this->get("/seasons/$seasonId/competitions")
             ->assertRedirect('/login');

        $this->get("/seasons/$seasonId/competitions/create")
             ->assertRedirect('/login');

        $this->post("/seasons/$seasonId/competitions")
             ->assertRedirect('/login');

        $this->get("/seasons/$seasonId/competitions/{$competition->getId()}/edit")
             ->assertRedirect('/login');

        $this->put("/seasons/$seasonId/competitions/{$competition->getId()}")
             ->assertRedirect('/login');

        $this->delete("/seasons/$seasonId/competitions/{$competition->getId()}")
             ->assertRedirect('/login');
    }

    public function testAccessForUsersWithoutAnyCorrectRoles(): void
    {
        $competition = Competition::factory()->create();
        $seasonId = $competition->getSeason()->getId();

        $this->be(User::factory()->create());

        $this->get("/seasons/$seasonId/competitions")
             ->assertForbidden();

        $this->get("/seasons/$seasonId/competitions/create")
             ->assertForbidden();

        $this->post("/seasons/$seasonId/competitions")
             ->assertForbidden();

        $this->get("/seasons/$seasonId/competitions/{$competition->getId()}/edit")
             ->assertForbidden();

        $this->put("/seasons/$seasonId/competitions/{$competition->getId()}")
             ->assertForbidden();

        $this->delete("/seasons/$seasonId/competitions/{$competition->getId()}")
             ->assertForbidden();
    }

    public function testAccessForSiteAdministrators(): void
    {
        $competition = Competition::factory()->make();
        $seasonId = $competition->getSeason()->getId();

        $this->be($this->siteAdmin);

        $this->get("/seasons/$seasonId/competitions")
             ->assertOk();

        $this->get("/seasons/$seasonId/competitions/create")
             ->assertOk();

        $this->post("/seasons/$seasonId/competitions", $competition->toArray())
             ->assertRedirect("/seasons/$seasonId/competitions");

        $competition = Competition::first();

        $this->get("/seasons/$seasonId/competitions/{$competition->getId()}/edit")
             ->assertOk();

        $this->put("/seasons/$seasonId/competitions/{$competition->getId()}", $competition->toArray())
             ->assertRedirect("/seasons/$seasonId/competitions");

        $this->delete("/seasons/$seasonId/competitions/{$competition->getId()}")
             ->assertRedirect("/seasons/$seasonId/competitions");
    }

    public function testAccessForUnverifiedUsers(): void
    {
        $competition = Competition::factory()->create();
        $seasonId = $competition->getSeason()->getId();

        $this->be(User::factory()->unverified()->create());

        $this->get("/seasons/$seasonId/competitions")
             ->assertRedirect('/email/verify');

        $this->get("/seasons/$seasonId/competitions/create")
             ->assertRedirect('/email/verify');

        $this->post("/seasons/$seasonId/competitions")
             ->assertRedirect('/email/verify');

        $this->get("/seasons/$seasonId/competitions/{$competition->getId()}/edit")
             ->assertRedirect('/email/verify');

        $this->put("/seasons/$seasonId/competitions/{$competition->getId()}")
             ->assertRedirect('/email/verify');

        $this->delete("/seasons/$seasonId/competitions/".($competition->getId()))
             ->assertRedirect('/email/verify');
    }

    public function testAccessForCompetitionAdministrators(): void
    {
        $season = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition */
        $competition = Competition::factory()->for($season)->create();

        $this->be(User::factory()->create()->assignRole(RolesHelper::competitionAdmin($competition)));

        $this->get("/seasons/{$season->getId()}/competitions")
             ->assertOk();

        $this->get("/seasons/{$season->getId()}/competitions/create")
             ->assertForbidden();

        $this->post("/seasons/{$season->getId()}/competitions", $competition->toArray())
             ->assertForbidden();

        $this->get("/seasons/{$season->getId()}/competitions/{$competition->getId()}/edit")
             ->assertOk();

        $this->put("/seasons/{$season->getId()}/competitions/{$competition->getId()}", $competition->toArray())
             ->assertRedirect("/seasons/{$season->getId()}/competitions");

        $this->delete("/seasons/{$season->getId()}/competitions/{$competition->getId()}")
             ->assertForbidden();

        $anotherCompetition = Competition::factory()->for($season)->create();

        $this->get("/seasons/{$season->getId()}/competitions/{$anotherCompetition->getId()}/edit")
             ->assertForbidden();

        $this->put(
            "/seasons/{$season->getId()}/competitions/{$anotherCompetition->getId()}",
            $anotherCompetition->toArray()
        )
             ->assertForbidden();

        $this->delete("/seasons/{$season->getId()}/competitions/{$anotherCompetition->getId()}")
             ->assertForbidden();

        $anotherSeasons = Season::factory()->create(['year' => 2001]);
        $yetAnotherCompetition = Competition::factory()->for($anotherSeasons)->create();

        $this->get("/seasons/{$anotherSeasons->getId()}/competitions")
             ->assertForbidden();

        $this->get("/seasons/{$anotherSeasons->getId()}/competitions/create")
             ->assertForbidden();

        $this->post("/seasons/{$anotherSeasons->getId()}/competitions", $competition->toArray())
             ->assertForbidden();

        $this->get("/seasons/{$anotherSeasons->getId()}/competitions/{$yetAnotherCompetition->getId()}/edit")
             ->assertForbidden();

        $this->put(
            "/seasons/{$anotherSeasons->getId()}/competitions/{$yetAnotherCompetition->getId()}",
            $competition->toArray()
        )
             ->assertForbidden();

        $this->delete("/seasons/{$anotherSeasons->getId()}/competitions/{$yetAnotherCompetition->getId()}")
             ->assertForbidden();
    }

    public function testAccessForSeasonAdministrators(): void
    {
        $competition = Competition::factory()->make();
        $seasonId = $competition->getSeason()->getId();

        $this->be(User::factory()->create()->assignRole(RolesHelper::seasonAdmin($competition->getSeason())));

        $this->get("/seasons/$seasonId/competitions")
             ->assertOk();

        $this->get("/seasons/$seasonId/competitions/create")
             ->assertOk();

        $this->post("/seasons/$seasonId/competitions", $competition->toArray())
             ->assertRedirect("/seasons/$seasonId/competitions");

        $competition = Competition::first();

        $this->get("/seasons/$seasonId/competitions/{$competition->getId()}/edit")
             ->assertOk();

        $this->put("/seasons/$seasonId/competitions/{$competition->getId()}", $competition->toArray())
             ->assertRedirect("/seasons/$seasonId/competitions");

        $this->delete("/seasons/$seasonId/competitions/{$competition->getId()}")
             ->assertRedirect("/seasons/$seasonId/competitions");
    }

    public function testAddingACompetition(): void
    {
        $season = Season::factory()->create();

        $this->be($this->siteAdmin);

        $this->post("/seasons/{$season->getId()}/competitions", [])
             ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseMissing(
            'competitions',
            ['season_id' => $season->getId(), 'name' => 'London League - Men']
        );

        $this->post("/seasons/{$season->getId()}/competitions", ['name' => 'London League - Men'])
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competitions', ['season_id' => $season->getId(), 'name' => 'London League - Men']);

        $this->post("/seasons/{$season->getId()}/competitions", ['name' => 'London League - Men'])
             ->assertSessionHasErrors('name', 'The competition already exists.');
        $this->assertDatabaseHas('competitions', ['season_id' => $season->getId(), 'name' => 'London League - Men']);

        Competition::factory()->create(['name' => 'Youth Games']);
        $this->post("/seasons/{$season->getId()}/competitions", ['name' => 'Youth Games'])
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competitions', ['season_id' => $season->getId(), 'name' => 'Youth Games']);
    }

    public function testEditingACompetition(): void
    {
        $competition = Competition::factory()->create(['name' => 'London League - Men']);
        $season = $competition->getSeason();

        $this->be($this->siteAdmin);

        $this->put("/seasons/{$season->getId()}/competitions/{$competition->getId()}", [])
             ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseHas('competitions', ['season_id' => $season->getId(), 'name' => 'London League - Men']);

        $this->put(
            "/seasons/{$season->getId()}/competitions/{$competition->getId()}",
            ['name' => 'London League - Women']
        )
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competitions', ['season_id' => $season->getId(), 'name' => 'London League - Women']);
        $this->assertDatabaseMissing(
            'competitions',
            ['season_id' => $season->getId(), 'name' => 'London League - Men']
        );

        Competition::factory()->for($season)->create(['name' => 'University League']);

        $this->put("/seasons/{$season->getId()}/competitions/{$competition->getId()}", ['name' => 'University League'])
             ->assertSessionHasErrors('name', 'The competition already exists in this season.');
        $this->assertDatabaseHas('competitions', ['season_id' => $season->getId(), 'name' => 'London League - Women']);

        $youthGames = Competition::factory()->create(['name' => 'Youth Games']);

        $this->put("/seasons/{$season->getId()}/competitions/{$competition->getId()}", ['name' => 'Youth Games'])
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competitions', ['season_id' => $season->getId(), 'name' => 'Youth Games']);
        $this->assertDatabaseHas(
            'competitions',
            ['season_id' => $youthGames->getSeason()->getId(), 'name' => 'Youth Games']
        );
    }

    public function testDeletingACompetition(): void
    {
        $competition = Competition::factory()->create(['name' => 'London League - Men']);
        $seasonId = $competition->getSeason()->getId();

        $this->be($this->siteAdmin);

        $this->delete("/seasons/$seasonId/competitions/{$competition->getId()}")
             ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('competitions', ['name' => 'London League - Men']);

        $this->delete("/seasons/$seasonId/competitions/".($competition->getId() + 1))
             ->assertNotFound();
    }

    public function testAddingCompetitionWillDispatchTheEvent(): void
    {
        Event::fake();

        $season = Season::factory()->create();
        $this->actingAs($this->siteAdmin)
             ->post("/seasons/{$season->getId()}/competitions", ['name' => 'London League - Men']);

        Event::assertDispatched(CompetitionCreated::class);
    }
}
