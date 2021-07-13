<?php

namespace Tests\Feature\CRUD;

use App\Events\SeasonCreated;
use App\Helpers\RolesHelper;
use App\Models\Season;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SeasonTest extends TestCase
{
    public function testAccessForGuests(): void
    {
        /** @var Season $season */
        $season = Season::factory()->create();

        $this->get('/seasons')
             ->assertRedirect('/login');

        $this->get('/seasons/create')
             ->assertRedirect('/login');

        $this->post('/seasons')
             ->assertRedirect('/login');

        $this->get("/seasons/{$season->getId()}/edit")
             ->assertRedirect('/login');

        $this->put("/seasons/{$season->getId()}")
             ->assertRedirect('/login');

        $this->delete("/seasons/{$season->getId()}")
             ->assertRedirect('/login');
    }

    public function testAccessForUsersWithoutAnyCorrectRoles(): void
    {
        $season = Season::factory()->create();

        $this->be(User::factory()->create());

        $this->get('/seasons')
             ->assertForbidden();

        $this->get('/seasons/create')
             ->assertForbidden();

        $this->post('/seasons', $season->toArray())
             ->assertForbidden();

        $this->get("/seasons/{$season->getId()}/edit")
             ->assertForbidden();

        $this->put("/seasons/{$season->getId()}", $season->toArray())
             ->assertForbidden();

        $this->delete("/seasons/{$season->getId()}")
             ->assertForbidden();
    }

    public function testAccessForSiteAdministrators(): void
    {
        $season = Season::factory()->make();

        $this->be($this->siteAdmin);

        $this->get('/seasons')
             ->assertOk();

        $this->get('/seasons/create')
             ->assertOk();

        $this->post('/seasons', $season->toArray())
             ->assertRedirect('/seasons');

        $season = Season::first();

        $this->get("/seasons/{$season->getId()}/edit")
             ->assertOk();

        $this->put("/seasons/{$season->getId()}", $season->toArray())
             ->assertRedirect('seasons');

        $this->delete("/seasons/{$season->getId()}")
             ->assertRedirect('seasons');
    }

    public function testAccessForUnverifiedUsers(): void
    {
        $season = Season::factory()->create();

        $this->be(User::factory()->unverified()->create());

        $this->get('/seasons')
             ->assertRedirect('/email/verify');

        $this->get('/seasons/create')
             ->assertRedirect('/email/verify');

        $this->post('/seasons')
             ->assertRedirect('/email/verify');

        $this->get("/seasons/{$season->getId()}/edit")
             ->assertRedirect('/email/verify');

        $this->put("/seasons/{$season->getId()}")
             ->assertRedirect('/email/verify');

        $this->delete("/seasons/{$season->getId()}")
             ->assertRedirect('/email/verify');
    }

    public function testAccessForSeasonAdministrators(): void
    {
        /** @var Season $season */
        $season = Season::factory()->create();

        $this->be(User::factory()->create()->assignRole(RolesHelper::seasonAdmin($season)));

        $this->get('/seasons')
             ->assertOk();

        $this->get('/seasons/create')
             ->assertForbidden();

        $this->post('/seasons', $season->toArray())
             ->assertForbidden();

        $this->get("/seasons/{$season->getId()}/edit")
             ->assertOk();

        $this->put("/seasons/{$season->getId()}", $season->toArray())
             ->assertRedirect('seasons');

        $this->delete("/seasons/{$season->getId()}")
             ->assertForbidden();

        $anotherSeason = Season::factory()->create();

        $this->get("/seasons/{$anotherSeason->getId()}/edit")
             ->assertForbidden();

        $this->put("/seasons/{$anotherSeason->getId()}", $season->toArray())
             ->assertForbidden();
    }

    public function testAddingASeason(): void
    {
        Event::fake();

        $this->be($this->siteAdmin);

        $this->post('/seasons', [])
             ->assertSessionHasErrors('year', 'The year is required.');
        $this->assertDatabaseMissing('seasons', ['year' => '2000']);
        Event::assertNotDispatched(SeasonCreated::class);

        $this->post('/seasons', ['year' => 'Twothousand'])
             ->assertSessionHasErrors('year', 'The year is not valid.');
        $this->assertDatabaseMissing('seasons', ['year' => '2000']);
        Event::assertNotDispatched(SeasonCreated::class);

        $this->post('/seasons', ['year' => '2000'])
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('seasons', ['year' => '2000']);
        Event::assertDispatchedTimes(SeasonCreated::class, 1);
        Event::assertDispatched(
            SeasonCreated::class,
            function (SeasonCreated $event): bool {
                return $event->season->getYear() === 2000;
            }
        );

        $this->post('/seasons', ['year' => '2000'])
             ->assertSessionHasErrors('year', 'The season already exists.');
        $this->assertDatabaseHas('seasons', ['year' => '2000']);
        Event::assertDispatchedTimes(SeasonCreated::class, 1);
    }

    public function testEditingASeason(): void
    {
        /** @var Season $season */
        $season = Season::factory()->create(['year' => '2000']);

        $this->be($this->siteAdmin);

        $this->put("/seasons/{$season->getId()}", [])
             ->assertSessionHasErrors('year', 'The year is required.');
        $this->assertDatabaseHas('seasons', ['year' => '2000']);

        $this->put("/seasons/{$season->getId()}", ['year' => 'Twothousand and one'])
             ->assertSessionHasErrors('year', 'The year is not valid.');
        $this->assertDatabaseHas('seasons', ['year' => '2000']);

        $this->put("/seasons/{$season->getId()}", ['year' => '2001'])
             ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('seasons', ['year' => 2000])
             ->assertDatabaseHas('seasons', ['year' => '2001']);

        Season::factory()->create(['year' => '1999']);

        $this->put("/seasons/{$season->getId()}", ['year' => '1999'])
             ->assertSessionHasErrors('year', 'The season already exists.');
        $this->assertDatabaseHas('seasons', ['year' => '2001']);

        $this->put('/seasons/2002')
             ->assertNotFound();
    }

    public function testDeletingASeason(): void
    {
        /** @var Season $season */
        $season = Season::factory()->create(['year' => '2000']);

        $this->be($this->siteAdmin);

        $this->delete("/seasons/{$season->getId()}")
             ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('seasons', ['year' => '2000']);

        $this->delete("/seasons/{$season->getId()}")
             ->assertNotFound();
    }

    public function testAddingSeasonWillDispatchTheEvent(): void
    {
        Event::fake();

        $this->actingAs($this->siteAdmin)
             ->post('/seasons', ['year' => '2000']);

        Event::assertDispatched(SeasonCreated::class);
    }
}
