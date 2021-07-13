<?php

namespace Tests\Feature\CRUD;

use App\Models\Club;
use App\Models\User;
use App\Models\Venue;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

class VenueTest extends TestCase
{
    public function testAccessForGuests(): void
    {
        $venue = Venue::factory()->create();

        $this->get('/venues')
             ->assertRedirect('/login');

        $this->get("/venues/{$venue->getId()}")
             ->assertRedirect('/login');

        $this->get('/venues/create')
             ->assertRedirect('/login');

        $this->post('/venues')
             ->assertRedirect('/login');

        $this->get("/venues/{$venue->getId()}/edit")
             ->assertRedirect('/login');

        $this->put("/venues/{$venue->getId()}")
             ->assertRedirect('/login');

        $this->delete("/venues/{$venue->getId()}")
             ->assertRedirect('/login');
    }

    public function testAccessForUsersWithoutAnyCorrectRoles(): void
    {
        $venue = Venue::factory()->create();

        $this->be(User::factory()->create());

        $this->get('/venues')
             ->assertForbidden();

        $this->get("/venues/{$venue->getId()}")
             ->assertForbidden();

        $this->get('/venues/create')
             ->assertForbidden();

        $this->post('/venues')
             ->assertForbidden();

        $this->get("/venues/{$venue->getId()}/edit")
             ->assertForbidden();

        $this->put("/venues/{$venue->getId()}")
             ->assertForbidden();

        $this->delete("/venues/{$venue->getId()}")
             ->assertForbidden();
    }

    public function testAccessForSiteAdministrators(): void
    {
        $venue = Venue::factory()->make();

        $this->be($this->siteAdmin);

        $this->get('/venues')
             ->assertOk();

        $this->get('/venues/create')
             ->assertOk();

        $this->post('/venues', $venue->toArray())
             ->assertRedirect('/venues');

        $venue = Venue::first();

        $this->get("/venues/{$venue->getId()}")
             ->assertOk();

        $this->get("/venues/{$venue->getId()}/edit")
             ->assertOk();

        $this->put("/venues/{$venue->getId()}", $venue->toArray())
             ->assertRedirect('venues');

        $this->delete("/venues/{$venue->getId()}")
             ->assertRedirect('venues');
    }

    public function testAccessForUnverifiedUsers(): void
    {
        $venue = Venue::factory()->create();

        $this->be(User::factory()->unverified()->create());

        $this->get('/venues')
             ->assertRedirect('/email/verify');

        $this->get("/venues/{$venue->getId()}")
             ->assertRedirect('/email/verify');

        $this->get('/venues/create')
             ->assertRedirect('/email/verify');

        $this->post('/venues')
             ->assertRedirect('/email/verify');

        $this->get("/venues/{$venue->getId()}/edit")
             ->assertRedirect('/email/verify');

        $this->put("/venues/{$venue->getId()}")
             ->assertRedirect('/email/verify');

        $this->delete("/venues/{$venue->getId()}")
             ->assertRedirect('/email/verify');
    }

    public function testAddingAVenue(): void
    {
        $this->be($this->siteAdmin);

        $this->post('/venues', [])
             ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseMissing('venues', ['name' => 'Olympic Stadium']);

        $this->post('/venues', ['name' => 'Olympic Stadium'])
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('venues', ['name' => 'Olympic Stadium']);

        $this->post('/venues', ['name' => 'Olympic Stadium'])
             ->assertSessionHasErrors('name', 'The venue already exists.');
        $this->assertDatabaseHas('venues', ['name' => 'Olympic Stadium']);
    }

    /**
     * @throws \Exception
     */
    public function testEditingAVenue(): void
    {
        $this->be($this->siteAdmin);

        $this->put('/venues/'.Uuid::generate()->string)
             ->assertNotFound();

        $venue = Venue::factory()->create(['name' => 'Olympic Stadium']);

        $this->put("/venues/{$venue->getId()}", [])
             ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseHas('venues', ['name' => 'Olympic Stadium']);

        $this->put("/venues/{$venue->getId()}", ['name' => 'Sobell S.C.'])
             ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('venues', ['name' => 'Olympic Stadium'])
             ->assertDatabaseHas('venues', ['name' => 'Sobell S.C.']);

        Venue::factory()->create(['name' => 'University of Westminster']);

        $this->put("/venues/{$venue->getId()}", ['name' => 'University of Westminster'])
             ->assertSessionHasErrors('name', 'The venue already exists.');
        $this->assertDatabaseHas('venues', ['name' => 'Sobell S.C.']);
    }

    /**
     * @throws \Exception
     */
    public function testDeletingAVenue(): void
    {
        $this->be($this->siteAdmin);

        $this->delete('/venues/'.Uuid::generate()->string)
             ->assertNotFound();

        $venue = Venue::factory()->create(['name' => 'Olympic Stadium']);
        Club::factory()->for($venue)->create(['name' => 'West Ham VC']);

        $this->delete("/venues/{$venue->getId()}")
             ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('venues', ['name' => 'Olympic Stadium'])
             ->assertDatabaseHas('clubs', ['name' => 'West Ham VC', 'venue_id' => null]);
    }
}
