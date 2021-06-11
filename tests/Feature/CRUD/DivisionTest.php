<?php

namespace Tests\Feature\CRUD;

use App\Events\DivisionCreated;
use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DivisionTest extends TestCase
{
    public function testAccessForGuests(): void
    {
        $division = Division::factory()->create();
        $competition = $division->getCompetition();

        $this->get("/competitions/{$competition->getId()}/divisions")
             ->assertRedirect('/login');

        $this->get("/competitions/{$competition->getId()}/divisions/create")
             ->assertRedirect('/login');

        $this->post("/competitions/{$competition->getId()}/divisions")
             ->assertRedirect('/login');

        $this->get("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
             ->assertRedirect('/login');

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertRedirect('/login');

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertRedirect('/login');
    }

    public function testAccessForUsersWithoutThePermission(): void
    {
        $division = Division::factory()->create();
        $competition = $division->getCompetition();

        $this->be(User::factory()->create());

        $this->get("/competitions/{$competition->getId()}/divisions")
             ->assertForbidden();

        $this->get("/competitions/{$competition->getId()}/divisions/create")
             ->assertForbidden();

        $this->post("/competitions/{$competition->getId()}/divisions")
             ->assertForbidden();

        $this->get("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
             ->assertForbidden();

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertForbidden();

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertForbidden();
    }

    public function testAccessForSiteAdministrators(): void
    {
        $division = Division::factory()->make();
        $competitionId = $division->getCompetition()->getId();

        $this->be($this->siteAdmin);

        $this->get("/competitions/$competitionId/divisions")
             ->assertOk();

        $this->get("/competitions/$competitionId/divisions/create")
             ->assertOk();

        $this->post("/competitions/$competitionId/divisions", $division->toArray())
             ->assertRedirect("/competitions/$competitionId/divisions");

        $division = Division::first();

        $this->get("/competitions/$competitionId/divisions/{$division->getId()}/edit")
             ->assertOk();

        $this->put("/competitions/$competitionId/divisions/{$division->getId()}", $division->toArray())
             ->assertRedirect("/competitions/$competitionId/divisions");

        $this->delete("/competitions/$competitionId/divisions/{$division->getId()}")
             ->assertRedirect("/competitions/$competitionId/divisions");
    }

    public function testAccessForUnverifiedUsers(): void
    {
        $division = Division::factory()->create();
        $competition = $division->getCompetition();

        $this->be(User::factory()->unverified()->create());

        $this->get("/competitions/{$competition->getId()}/divisions")
             ->assertRedirect('/email/verify');

        $this->get("/competitions/{$competition->getId()}/divisions/create")
             ->assertRedirect('/email/verify');

        $this->post("/competitions/{$competition->getId()}/divisions")
             ->assertRedirect('/email/verify');

        $this->get("/competitions/{$competition->getId()}/divisions/create")
             ->assertRedirect('/email/verify');

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertRedirect('/email/verify');

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertRedirect('/email/verify');
    }

    public function testAccessForSeasonAdministrators(): void
    {
        $division = Division::factory()->make();
        $competition = $division->getCompetition();

        $seasonAdmin = User::factory()
                           ->create()
                           ->assignRole(RolesHelper::seasonAdmin($division->getCompetition()->getSeason()));
        $this->be($seasonAdmin);

        $this->get("/competitions/{$competition->getId()}/divisions")
             ->assertOk();

        $this->get("/competitions/{$competition->getId()}/divisions/create")
             ->assertOk();

        $this->post("/competitions/{$competition->getId()}/divisions", $division->toArray())
             ->assertRedirect("/competitions/{$competition->getId()}/divisions");

        $division = Division::first();

        $this->get("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
             ->assertOk();

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", $division->toArray())
             ->assertRedirect("/competitions/{$competition->getId()}/divisions");

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertRedirect("/competitions/{$competition->getId()}/divisions");
    }

    public function testAccessForCompetitionAdministrators(): void
    {
        $season = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition */
        $competition = Competition::factory()->for($season)->create();
        $division = Division::factory()->for($competition)->make();

        $this->be(User::factory()->create()->assignRole(RolesHelper::competitionAdmin($competition)));

        $this->get("/competitions/{$competition->getId()}/divisions")
             ->assertOk();

        $this->get("/competitions/{$competition->getId()}/divisions/create")
             ->assertOk();

        $this->post("/competitions/{$competition->getId()}/divisions", $division->toArray())
             ->assertRedirect("/competitions/{$competition->getId()}/divisions");

        $division = Division::first();

        $this->get("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
             ->assertOk();

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", $division->toArray())
             ->assertRedirect("/competitions/{$competition->getId()}/divisions");

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertRedirect("/competitions/{$competition->getId()}/divisions");

        // Recreate a division so the rest of the test can work
        $division = Division::factory()->for($competition)->create();

        // Competition Admin for a different competition in the same season
        /** @var Competition $anotherCompetition */
        $anotherCompetition = Competition::factory()->for($season)->create();

        $this->be(User::factory()->create()->assignRole(RolesHelper::competitionAdmin($anotherCompetition)));

        $this->get("/competitions/{$competition->getId()}/divisions")
             ->assertForbidden();

        $this->get("/competitions/{$competition->getId()}/divisions/create")
             ->assertForbidden();

        $this->post("/competitions/{$competition->getId()}/divisions", $division->toArray())
             ->assertForbidden();

        $this->get("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
             ->assertForbidden();

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", $division->toArray())
             ->assertForbidden();

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertForbidden();

        // Competition Admin for a different competition in a different season
        $anotherSeason = Season::factory()->create(['year' => 2001]);
        /** @var Competition $yetAnotherCompetition */
        $yetAnotherCompetition = Competition::factory()->for($anotherSeason)->create();

        $this->be(User::factory()->create()->assignRole(RolesHelper::competitionAdmin($yetAnotherCompetition)));

        $this->get("/competitions/{$competition->getId()}/divisions")
             ->assertForbidden();

        $this->get("/competitions/{$competition->getId()}/divisions/create")
             ->assertForbidden();

        $this->post("/competitions/{$competition->getId()}/divisions", $division->toArray())
             ->assertForbidden();

        $this->get("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
             ->assertForbidden();

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", $division->toArray())
             ->assertForbidden();

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertForbidden();
    }

    public function testAccessForDivisionAdministrators(): void
    {
        $season = Season::factory()->create(['year' => 2000]);
        $competition = Competition::factory()->for($season)->create();
        /** @var Division $division */
        $division = Division::factory()->for($competition)->create();

        $this->be(User::factory()->create()->assignRole(RolesHelper::divisionAdmin($division)));

        $this->get("/competitions/{$competition->getId()}/divisions")
             ->assertOk();

        $this->get("/competitions/{$competition->getId()}/divisions/create")
             ->assertForbidden();

        $this->post("/competitions/{$competition->getId()}/divisions", $division->toArray())
             ->assertForbidden();

        $this->get("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
             ->assertOk();

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", $division->toArray())
             ->assertRedirect("/competitions/{$competition->getId()}/divisions");

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertForbidden();

        // Division Administrator for another division in the same competition
        /** @var Division $anotherDivision */
        $anotherDivision = Division::factory()->for($competition)->create();

        $this->be(User::factory()->create()->assignRole(RolesHelper::divisionAdmin($anotherDivision)));

        $this->get("/competitions/{$competition->getId()}/divisions")
             ->assertOk();

        $this->get("/competitions/{$competition->getId()}/divisions/create")
             ->assertForbidden();

        $this->post("/competitions/{$competition->getId()}/divisions", $division->toArray())
             ->assertForbidden();

        $this->get("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
             ->assertForbidden();

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", $division->toArray())
             ->assertForbidden();

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertForbidden();

        // Division Administrator in another competition and season
        $anotherSeason = Season::factory()->create(['year' => 2001]);
        $anotherCompetition = Competition::factory()->for($anotherSeason)->create();
        /** @var Division $yetAnotherDivision */
        $yetAnotherDivision = Division::factory()->for($anotherCompetition)->create();

        $this->be(User::factory()->create()->assignRole(RolesHelper::divisionAdmin($yetAnotherDivision)));

        $this->get("/competitions/{$competition->getId()}/divisions")
             ->assertForbidden();

        $this->get("/competitions/{$competition->getId()}/divisions/create")
             ->assertForbidden();

        $this->post("/competitions/{$competition->getId()}/divisions", $division->toArray())
             ->assertForbidden();

        $this->get("/competitions/{$competition->getId()}/divisions/{$division->getId()}/edit")
             ->assertForbidden();

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", $division->toArray())
             ->assertForbidden();

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertForbidden();
    }

    public function testAddingADivision(): void
    {
        $this->be($this->siteAdmin);

        $this->post('/competitions/1/divisions', [])
             ->assertNotFound();
        $this->assertDatabaseMissing('divisions', ['name' => 'MP', 'display_order' => 1]);

        $competition = Competition::factory()->create();

        $this->post(
            "/competitions/{$competition->getId()}/divisions",
            [
                'competition_id' => $competition->getId(),
            ]
        )
             ->assertSessionHasErrors('name', 'The name is required.')
             ->assertSessionHasErrors('display_order', 'The order is required.');
        $this->assertDatabaseMissing(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ]
        );

        $this->post(
            "/competitions/{$competition->getId()}/divisions",
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 'A',
            ]
        )
             ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseMissing(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ]
        );

        $this->post(
            "/competitions/{$competition->getId()}/divisions",
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => -1,
            ]
        )
             ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseMissing(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',

                'display_order' => 1,
            ]
        );

        $this->post(
            "/competitions/{$competition->getId()}/divisions",
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 0,
            ]
        )
             ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseMissing(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ]
        );

        $this->post(
            "/competitions/{$competition->getId()}/divisions",
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ]
        )
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ]
        );

        $this->post(
            "/competitions/{$competition->getId()}/divisions",
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ]
        )
             ->assertSessionHasErrors('name', 'The division already exists.');
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ]
        );

        $this->post(
            "/competitions/{$competition->getId()}/divisions",
            [
                'competition_id' => $competition->getId(),
                'name' => 'DIV1M',
                'display_order' => 1,
            ]
        )
             ->assertSessionHasErrors('display_order', 'The order is already used for another division.');
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ]
        );
        $this->assertDatabaseMissing(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'DIV1M',

                'display_order' => 1,
            ]
        );

        $this->post(
            "/competitions/{$competition->getId()}/divisions",
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 2,
            ]
        )
             ->assertSessionHasErrors('name', 'The division already exists.');
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ]
        );
        $this->assertDatabaseMissing(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 2,
            ]
        );

        Division::factory()->create(['name' => 'DIV1M', 'display_order' => 1]);

        $this->post(
            "/competitions/{$competition->getId()}/divisions",
            [
                'competition_id' => $competition->getId(),
                'name' => 'DIV1M',
                'display_order' => 2,
            ]
        )
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'DIV1M',
                'display_order' => 2,
            ]
        );
    }

    public function testEditingADivision(): void
    {
        $this->be($this->siteAdmin);

        $this->put('/competitions/1/divisions/1', [])
             ->assertNotFound();

        $competition = Competition::factory()->create();

        $this->put("/competitions/{$competition->getId()}/divisions/1", [])
             ->assertNotFound();

        $division = Division::factory()->for($competition)->create(['name' => 'MP', 'display_order' => 1]);

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", [])
             ->assertSessionHasErrors('name', 'The name is required.')
             ->assertSessionHasErrors('display_order', 'The order is required.');
        $this->assertDatabaseHas('divisions', ['name' => 'MP', 'display_order' => 1]);

        $this->put(
            "/competitions/{$competition->getId()}/divisions/{$division->getId()}",
            [
                'name' => 'MP',
                'display_order' => 'A',
            ]
        )
             ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ]
        );

        $this->put(
            "/competitions/{$competition->getId()}/divisions/{$division->getId()}",
            [
                'name' => 'MP',
                'display_order' => 0,
            ]
        )
             ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ]
        );

        $this->put(
            "/competitions/{$competition->getId()}/divisions/{$division->getId()}",
            [
                'name' => 'MP',
                'display_order' => -1,
            ]
        )
             ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ]
        );

        $this->put(
            "/competitions/{$competition->getId()}/divisions/{$division->getId()}",
            [
                'name' => 'DIV1M',
                'display_order' => 1,
            ]
        )
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'DIV1M',
                'display_order' => 1,
            ]
        );

        $this->put(
            "/competitions/{$competition->getId()}/divisions/{$division->getId()}",
            [
                'name' => 'DIV1M',
                'display_order' => 1,
            ]
        )
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'DIV1M',
                'display_order' => 1,
            ]
        );

        Division::factory()->for($competition)->create(['name' => 'DIV2M', 'display_order' => 2]);

        $this->put(
            "/competitions/{$competition->getId()}/divisions/{$division->getId()}",
            [
                'name' => 'DIV2M',
                'display_order' => 1,
            ]
        )
             ->assertSessionHasErrors('name', 'The division already exists in this competition.');
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'DIV1M',
                'display_order' => 1,
            ]
        );

        $this->put(
            "/competitions/{$competition->getId()}/divisions/{$division->getId()}",
            [
                'name' => 'DIV2M',
                'display_order' => 2,
            ]
        )
             ->assertSessionHasErrors('display_order', 'The order is already used for another division.');
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'DIV1M',
                'display_order' => 1,
            ]
        );

        $div1W = Division::factory()->create(['name' => 'DIV1W', 'display_order' => 2]);

        $this->put(
            "/competitions/{$competition->getId()}/divisions/{$division->getId()}",
            [
                'name' => 'DIV1W',
                'display_order' => 1,
            ]
        )
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'DIV1W',
                'display_order' => 1,
            ]
        )->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $competition->getId(),
                'name' => 'DIV2M',
                'display_order' => 2,
            ]
        )->assertDatabaseHas(
            'divisions',
            [
                'competition_id' => $div1W->getCompetition()->getId(),
                'name' => 'DIV1W',
                'display_order' => 2,
            ]
        );
    }

    public function testDeletingADivision(): void
    {
        $this->be($this->siteAdmin);

        $this->delete('/competitions/1/divisions/1')
             ->assertNotFound();

        $competition = Competition::factory()->create();

        $this->delete("/competitions/{$competition->getId()}/divisions/1")
             ->assertNotFound();

        $division = Division::factory()->for($competition)->create(['name' => 'MP', 'display_order' => 1]);

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
             ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing(
            'divisions',
            [
                'id' => $division->getId(),
                'deleted_at' => null,
            ]
        );
    }

    public function testAddingDivisionWillDispatchTheEvent(): void
    {
        Event::fake();

        $competition = Competition::factory()->create();
        $this->actingAs($this->siteAdmin)
             ->post(
                 "/competitions/{$competition->getId()}/divisions",
                 [
                     'competition_id' => $competition->getId(),
                     'name' => 'MP',
                     'display_order' => 1,
                 ]
             );

        Event::assertDispatched(DivisionCreated::class);
    }
}
