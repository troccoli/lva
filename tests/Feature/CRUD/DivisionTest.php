<?php

namespace Tests\Feature\CRUD;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DivisionTest extends TestCase
{
    use RefreshDatabase;

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

    public function testAccessForUserWithoutThePermission(): void
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

    public function testAccessForSuperAdmin(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->make();
        $competitionId = $division->getCompetition()->getId();

        $this->be(factory(User::class)->create()->assignRole('Super Admin'));

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

    public function testAddingADivision(): void
    {
        $this->be(factory(User::class)->create()->givePermissionTo('manage raw data'));

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
        $this->be(factory(User::class)->create()->givePermissionTo('manage raw data'));

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
        $this->be(factory(User::class)->create()->givePermissionTo('manage raw data'));

        $this->delete("/competitions/1/divisions/1")
            ->assertNotFound();

        $competition = factory(Competition::class)->create();

        $this->delete("/competitions/{$competition->getId()}/divisions/1")
            ->assertNotFound();

        /** @var Division $division */
        $division = factory(Division::class)->create([
            'competition_id' => $competition->getId(),
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->delete("/competitions/{$competition->getId()}/divisions/{$division->getId()}")
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('divisions', [
            'id'         => $division->getId(),
            'deleted_at' => null,
        ]);
    }
}
