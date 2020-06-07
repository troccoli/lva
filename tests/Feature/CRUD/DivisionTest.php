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
        /** @var Division $division */
        $division = factory(Division::class)->create();
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
        /** @var Division $division */
        $division = factory(Division::class)->create();
        $competition = $division->getCompetition();

        $this->be(factory(User::class)->create());

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
        /** @var Division $division */
        $division = factory(Division::class)->make();
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
        /** @var Division $division */
        $division = factory(Division::class)->create();
        $competition = $division->getCompetition();

        $this->be(factory(User::class)->state('unverified')->create());

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
        /** @var Division $division */
        $division = factory(Division::class)->make();
        $competitionId = $division->getCompetition()->getId();

        $this->actingAs(factory(User::class)
            ->create()
            ->assignRole(RolesHelper::seasonAdminName($division->getCompetition()->getSeason())));

        $this->get("/competitions/$competitionId/divisions")
            ->assertOk();

        $this->get("/competitions/$competitionId/divisions/create")
            ->assertOk();

        $this->post("/competitions/$competitionId/divisions", $division->toArray())
            ->assertRedirect("/competitions/$competitionId/divisions");

        $division = Division::first();

        $this->get("/competitions/$competitionId/divisions/" . $division->getId() . '/edit')
            ->assertOk();

        $this->put("/competitions/$competitionId/divisions/" . $division->getId(), $division->toArray())
            ->assertRedirect("/competitions/$competitionId/divisions");

        $this->delete("/competitions/$competitionId/divisions/" . $division->getId())
            ->assertRedirect("/competitions/$competitionId/divisions");
    }

    public function testAccessForCompetitionAdministrators(): void
    {
        $seasonId = factory(Season::class)->create(['year' => 2000])->getId();
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create(['season_id' => $seasonId]);
        $competitionId = $competition->getId();
        /** @var Division $division */
        $division = factory(Division::class)->make(['competition_id' => $competitionId]);

        $this->actingAs(factory(User::class)->create()->assignRole(RolesHelper::competitionAdminName($competition)));

        $this->get("/competitions/$competitionId/divisions")
            ->assertOk();

        $this->get("/competitions/$competitionId/divisions/create")
            ->assertOk();

        $this->post("/competitions/$competitionId/divisions", $division->toArray())
            ->assertRedirect("/competitions/$competitionId/divisions");

        $division = Division::first();

        $this->get("/competitions/$competitionId/divisions/" . $division->getId() . '/edit')
            ->assertOk();

        $this->put("/competitions/$competitionId/divisions/" . $division->getId(), $division->toArray())
            ->assertRedirect("/competitions/$competitionId/divisions");

        $this->delete("/competitions/$competitionId/divisions/" . $division->getId())
            ->assertRedirect("/competitions/$competitionId/divisions");

        // Recreate a division so the rest of the test can work
        $division = factory(Division::class)->create(['competition_id' => $competitionId]);

        // Competition Admin for a different competition in the same season
        /** @var Competition $anotherCompetition */
        $anotherCompetition = factory(Competition::class)->create(['season_id' => $seasonId]);

        $this->actingAs(factory(User::class)->create()->assignRole(RolesHelper::competitionAdminName($anotherCompetition)));

        $this->get("/competitions/$competitionId/divisions")
            ->assertForbidden();

        $this->get("/competitions/$competitionId/divisions/create")
            ->assertForbidden();

        $this->post("/competitions/$competitionId/divisions", $division->toArray())
            ->assertForbidden();

        $r = $this->get("/competitions/$competitionId/divisions/" . $division->getId() . '/edit');

        $this->get("/competitions/$competitionId/divisions/" . $division->getId() . '/edit')
            ->assertForbidden();

        $this->put("/competitions/$competitionId/divisions/" . $division->getId(), $division->toArray())
            ->assertForbidden();

        $this->delete("/competitions/$competitionId/divisions/" . $division->getId())
            ->assertForbidden();

        // Competition Admin for a different competition in a different season
        $anotherSeasonId = factory(Season::class)->create(['year' => 2001])->getId();
        /** @var Competition $yetAnotherCompetition */
        $yetAnotherCompetition = factory(Competition::class)->create(['season_id' => $anotherSeasonId]);

        $this->actingAs(factory(User::class)->create()->assignRole(RolesHelper::competitionAdminName($yetAnotherCompetition)));

        $this->get("/competitions/$competitionId/divisions")
            ->assertForbidden();

        $this->get("/competitions/$competitionId/divisions/create")
            ->assertForbidden();

        $this->post("/competitions/$competitionId/divisions", $division->toArray())
            ->assertForbidden();

        $this->get("/competitions/$competitionId/divisions/" . $division->getId() . '/edit')
            ->assertForbidden();

        $this->put("/competitions/$competitionId/divisions/" . $division->getId(), $division->toArray())
            ->assertForbidden();

        $this->delete("/competitions/$competitionId/divisions/" . $division->getId())
            ->assertForbidden();
    }

    public function testAccessForDivisionAdministrators(): void
    {
        $seasonId = factory(Season::class)->create(['year' => 2000])->getId();
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create(['season_id' => $seasonId]);
        $competitionId = $competition->getId();
        /** @var Division $division */
        $division = factory(Division::class)->create(['competition_id' => $competitionId]);

        $this->actingAs(factory(User::class)->create()->assignRole(RolesHelper::divisionAdminName($division)));

        $this->get("/competitions/$competitionId/divisions")
            ->assertOk();

        $this->get("/competitions/$competitionId/divisions/create")
            ->assertForbidden();

        $this->post("/competitions/$competitionId/divisions", $division->toArray())
            ->assertForbidden();

        $this->get("/competitions/$competitionId/divisions/" . $division->getId() . '/edit')
            ->assertOk();

        $this->put("/competitions/$competitionId/divisions/" . $division->getId(), $division->toArray())
            ->assertRedirect("/competitions/$competitionId/divisions");

        $this->delete("/competitions/$competitionId/divisions/" . $division->getId())
            ->assertForbidden();

        // Division Administrator for another division in the same competition
        /** @var Division $anotherDivision */
        $anotherDivision = factory(Division::class)->create(['competition_id' => $competitionId]);

        $this->actingAs(factory(User::class)->create()->assignRole(RolesHelper::divisionAdminName($anotherDivision)));

        $this->get("/competitions/$competitionId/divisions")
            ->assertOk();

        $this->get("/competitions/$competitionId/divisions/create")
            ->assertForbidden();

        $this->post("/competitions/$competitionId/divisions", $division->toArray())
            ->assertForbidden();

        $this->get("/competitions/$competitionId/divisions/" . $division->getId() . '/edit')
            ->assertForbidden();

        $this->put("/competitions/$competitionId/divisions/" . $division->getId(), $division->toArray())
            ->assertForbidden();

        $this->delete("/competitions/$competitionId/divisions/" . $division->getId())
            ->assertForbidden();

        // Division Administrator in another competition and season
        $anotherSeasonId = factory(Season::class)->create(['year' => 2001])->getId();
        $anotherCompetitionId = factory(Competition::class)->create(['season_id' => $anotherSeasonId])->getId();
        /** @var Division $yetAnotherDivision */
        $yetAnotherDivision = factory(Division::class)->create(['competition_id' => $anotherCompetitionId]);

        $this->actingAs(factory(User::class)->create()->assignRole(RolesHelper::divisionAdminName($yetAnotherDivision)));

        $this->get("/competitions/$competitionId/divisions")
            ->assertForbidden();

        $this->get("/competitions/$competitionId/divisions/create")
            ->assertForbidden();

        $this->post("/competitions/$competitionId/divisions", $division->toArray())
            ->assertForbidden();

        $this->get("/competitions/$competitionId/divisions/" . $division->getId() . '/edit')
            ->assertForbidden();

        $this->put("/competitions/$competitionId/divisions/" . $division->getId(), $division->toArray())
            ->assertForbidden();

        $this->delete("/competitions/$competitionId/divisions/" . $division->getId())
            ->assertForbidden();
    }

    public function testAddingADivision(): void
    {
        $this->be($this->siteAdmin);

        $this->post("/competitions/1/divisions", [])
            ->assertNotFound();
        $this->assertDatabaseMissing('divisions', ['name' => 'MP', 'display_order' => 1]);

        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();

        $this->post("/competitions/{$competition->getId()}/divisions", [
            'competition_id' => $competition->getId(),
        ])
            ->assertSessionHasErrors('name', 'The name is required.')
            ->assertSessionHasErrors('display_order', 'The order is required.');
        $this->assertDatabaseMissing('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->post("/competitions/{$competition->getId()}/divisions", [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 'A',
        ])
            ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseMissing('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->post("/competitions/{$competition->getId()}/divisions", [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => -1,
        ])
            ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseMissing('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',

            'display_order' => 1,
        ]);

        $this->post("/competitions/{$competition->getId()}/divisions", [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 0,
        ])
            ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseMissing('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->post("/competitions/{$competition->getId()}/divisions", [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->post("/competitions/{$competition->getId()}/divisions", [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ])
            ->assertSessionHasErrors('name', 'The division already exists.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->post("/competitions/{$competition->getId()}/divisions", [
            'competition_id' => $competition->getId(),
            'name'           => 'DIV1M',
            'display_order'  => 1,
        ])
            ->assertSessionHasErrors('display_order', 'The order is already used for another division.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ]);
        $this->assertDatabaseMissing('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'DIV1M',

            'display_order' => 1,
        ]);

        $this->post("/competitions/{$competition->getId()}/divisions", [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 2,
        ])
            ->assertSessionHasErrors('name', 'The division already exists.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ]);
        $this->assertDatabaseMissing('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 2,
        ]);

        factory(Division::class)->create(['name' => 'DIV1M', 'display_order' => 1]);

        $this->post("/competitions/{$competition->getId()}/divisions", [
            'competition_id' => $competition->getId(),
            'name'           => 'DIV1M',
            'display_order'  => 2,
        ])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'DIV1M',
            'display_order'  => 2,
        ]);
    }

    public function testEditingADivision(): void
    {
        $this->be($this->siteAdmin);

        $this->put("/competitions/1/divisions/1", [])
            ->assertNotFound();

        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();

        $this->put("/competitions/{$competition->getId()}/divisions/1", [])
            ->assertNotFound();

        /** @var Division $division */
        $division = factory(Division::class)->create([
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", [])
            ->assertSessionHasErrors('name', 'The name is required.')
            ->assertSessionHasErrors('display_order', 'The order is required.');
        $this->assertDatabaseHas('divisions', ['name' => 'MP', 'display_order' => 1]);

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", [
            'name'          => 'MP',
            'display_order' => 'A',
        ])
            ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", [
            'name'          => 'MP',
            'display_order' => 0,
        ])
            ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", [
            'name'          => 'MP',
            'display_order' => -1,
        ])
            ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", [
            'name'          => 'DIV1M',
            'display_order' => 1,
        ])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'DIV1M',
            'display_order'  => 1,
        ]);

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", [
            'name'          => 'DIV1M',
            'display_order' => 1,
        ])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'DIV1M',
            'display_order'  => 1,
        ]);

        factory(Division::class)->create([
            'competition_id' => $competition->getId(),
            'name'           => 'DIV2M',
            'display_order'  => 2,
        ]);

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", [
            'name'          => 'DIV2M',
            'display_order' => 1,
        ])
            ->assertSessionHasErrors('name', 'The division already exists in this competition.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'DIV1M',
            'display_order'  => 1,
        ]);

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", [
            'name'          => 'DIV2M',
            'display_order' => 2,
        ])
            ->assertSessionHasErrors('display_order', 'The order is already used for another division.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'DIV1M',
            'display_order'  => 1,
        ]);

        $div1W = factory(Division::class)->create(['name' => 'DIV1W', 'display_order' => 2]);

        $this->put("/competitions/{$competition->getId()}/divisions/{$division->getId()}", [
            'name'          => 'DIV1W',
            'display_order' => 1,
        ])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'DIV1W',
            'display_order'  => 1,
        ])->assertDatabaseHas('divisions', [
            'competition_id' => $competition->getId(),
            'name'           => 'DIV2M',
            'display_order'  => 2,
        ])->assertDatabaseHas('divisions', [
            'competition_id' => $div1W->getCompetition()->getId(),
            'name'           => 'DIV1W',
            'display_order'  => 2,
        ]);
    }

    public function testDeletingADivision(): void
    {
        $this->be($this->siteAdmin);

        $this->delete("/competitions/1/divisions/1")
            ->assertNotFound();

        $competition = factory(Competition::class)->create();

        $this->delete("/competitions/{$competition->getId()}/divisions/1")
            ->assertNotFound();

        /** @var Division $division */
        $division = factory(Division::class)->create([
            'competition_id' => $competition->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ]);

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('divisions', [
            'id' => $division->getId(),
            'deleted_at' => null,
        ]);
    }

    public function testAddingDivisionWillDispatchTheEvent(): void
    {
        Event::fake();

        $this->be($this->siteAdmin);
        $competitionId = factory(Competition::class)->create()->getId();

        $this->post("/competitions/$competitionId/divisions", [
            'competition_id' => $competitionId,
            'name' => 'MP',
            'display_order' => 1,
        ]);

        Event::assertDispatched(DivisionCreated::class);
    }
}
