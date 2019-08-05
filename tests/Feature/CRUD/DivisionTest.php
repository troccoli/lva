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
        $competitionId = $division->getCompetition()->getId();

        $this->get("/competitions/$competitionId/divisions")
            ->assertRedirect('/login');

        $this->get("/competitions/$competitionId/divisions/create")
            ->assertRedirect('/login');

        $this->post("/competitions/$competitionId/divisions")
            ->assertRedirect('/login');

        $this->get("/competitions/$competitionId/divisions/{$division->getId()}/edit")
            ->assertRedirect('/login');

        $this->put("/competitions/$competitionId/divisions/{$division->getId()}")
            ->assertRedirect('/login');

        $this->delete("/competitions/$competitionId/divisions/{$division->getId()}")
            ->assertRedirect('/login');
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->make();
        $competitionId = $division->getCompetition()->getId();

        $this->be(factory(User::class)->create());

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
        $competitionId = $division->getCompetition()->getId();

        $this->be(factory(User::class)->state('unverified')->create());

        $this->get("/competitions/$competitionId/divisions")
            ->assertRedirect('/email/verify');

        $this->get("/competitions/$competitionId/divisions/create")
            ->assertRedirect('/email/verify');

        $this->post("/competitions/$competitionId/divisions")
            ->assertRedirect('/email/verify');

        $this->get("/competitions/$competitionId/divisions/create")
            ->assertRedirect('/email/verify');

        $this->put("/competitions/$competitionId/divisions/{$division->getId()}")
            ->assertRedirect('/email/verify');

        $this->delete("/competitions/$competitionId/divisions/{$division->getId()}")
            ->assertRedirect('/email/verify');
    }

    public function testAddingADivision(): void
    {
        $this->actingAs(factory(User::class)->create());

        // Non-existing competition
        $this->post("/competitions/1/divisions", [])
            ->assertNotFound();
        $this->assertDatabaseMissing('divisions', ['name' => 'MP', 'display_order' => 1]);

        // Missing required fields
        $competitionId = factory(Competition::class)->create()->getId();

        $this->post("/competitions/$competitionId/divisions", [])
            ->assertSessionHasErrors('name', 'The name is required.')
            ->assertSessionHasErrors('display_order', 'The order is required.');
        $this->assertDatabaseMissing('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        // Wrong order
        $this->post("/competitions/$competitionId/divisions", [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 'A',
        ])
            ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseMissing('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->post("/competitions/$competitionId/divisions", [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => -1,
        ])
            ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseMissing('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order' => 1,
        ]);

        $this->post("/competitions/$competitionId/divisions", [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 0,
        ])
            ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseMissing('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        // OK
        $this->post("/competitions/$competitionId/divisions", [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        // Existing division
        $this->post("/competitions/$competitionId/divisions", [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ])
            ->assertSessionHasErrors('name', 'The division already exists.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ]);
        $this->post("/competitions/$competitionId/divisions", [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 2,
        ])
            ->assertSessionHasErrors('name', 'The division already exists.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ]);
        $this->assertDatabaseMissing('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 2,
        ]);

        // Same order as other division in same competition
        $this->post("/competitions/$competitionId/divisions", [
            'competition_id' => $competitionId,
            'name'           => 'DIV1M',
            'display_order'  => 1,
        ])
            ->assertSessionHasErrors('display_order', 'The order is already used for another division.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ]);
        $this->assertDatabaseMissing('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'DIV1M',
            'display_order' => 1,
        ]);

        // Same division in different competition
        factory(Division::class)->create(['name' => 'DIV1M', 'display_order' => 1]);
        $this->post("/competitions/$competitionId/divisions", [
            'competition_id' => $competitionId,
            'name'           => 'DIV1M',
            'display_order'  => 2,
        ])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'DIV1M',
            'display_order'  => 2,
        ]);
    }

    public function testEditingADivision(): void
    {
        $this->actingAs(factory(User::class)->create());

        $this->put("/competitions/1/divisions/1", [])
            ->assertNotFound();

        $competitionId = factory(Competition::class)->create()->getId();

        $this->put("/competitions/$competitionId/divisions/1", [])
            ->assertNotFound();

        /** @var Division $division */
        $division = factory(Division::class)->create([
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->put("/competitions/$competitionId/divisions/{$division->getId()}", [])
            ->assertSessionHasErrors('name', 'The name is required.')
            ->assertSessionHasErrors('display_order', 'The order is required.');
        $this->assertDatabaseHas('divisions', ['name' => 'MP', 'display_order' => 1]);

        $this->put("/competitions/$competitionId/divisions/{$division->getId()}", [
            'name'          => 'MP',
            'display_order' => 'A',
        ])
            ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->put("/competitions/$competitionId/divisions/{$division->getId()}", [
            'name'          => 'MP',
            'display_order' => 0,
        ])
            ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->put("/competitions/$competitionId/divisions/{$division->getId()}", [
            'name'          => 'MP',
            'display_order' => -1,
        ])
            ->assertSessionHasErrors('display_order', 'The order must be a positive number.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'MP',
            'display_order'  => 1,
        ]);

        $this->put("/competitions/$competitionId/divisions/{$division->getId()}", [
            'name'          => 'DIV1M',
            'display_order' => 1,
        ])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'DIV1M',
            'display_order'  => 1,
        ]);

        $this->put("/competitions/$competitionId/divisions/{$division->getId()}", [
            'name'          => 'DIV1M',
            'display_order' => 1,
        ])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'DIV1M',
            'display_order'  => 1,
        ]);

        factory(Division::class)->create([
            'competition_id' => $competitionId,
            'name'           => 'DIV2M',
            'display_order'  => 2,
        ]);

        $this->put("/competitions/$competitionId/divisions/{$division->getId()}", [
            'name'          => 'DIV2M',
            'display_order' => 1,
        ])
            ->assertSessionHasErrors('name', 'The division already exists in this competition.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'DIV1M',
            'display_order'  => 1,
        ]);

        $this->put("/competitions/$competitionId/divisions/{$division->getId()}", [
            'name'          => 'DIV2M',
            'display_order' => 2,
        ])
            ->assertSessionHasErrors('display_order', 'The order is already used for another division.');
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'DIV1M',
            'display_order'  => 1,
        ]);

        $div1W = factory(Division::class)->create(['name' => 'DIV1W', 'display_order' => 2]);

        $this->put("/competitions/$competitionId/divisions/{$division->getId()}", [
            'name'          => 'DIV1W',
            'display_order' => 1,
        ])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
            'name'           => 'DIV1W',
            'display_order'  => 1,
        ])->assertDatabaseHas('divisions', [
            'competition_id' => $competitionId,
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
        $this->actingAs(factory(User::class)->create());

        $this->delete("/competitions/1/divisions/1")
            ->assertNotFound();

        $competitionId = factory(Competition::class)->create()->getId();

        /** @var Division $division */
        $division = factory(Division::class)->create(['competition_id' => $competitionId]);

        $this->delete("/competitions/$competitionId/divisions/{$division->getId()}")
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('divisions', [
            'id'         => $division->getId(),
            'deleted_at' => null,
        ]);
        $this->delete("/competitions/$competitionId/divisions/{$division->getId()}")
            ->assertNotFound();
    }
}
