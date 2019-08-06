<?php

namespace Tests\Feature\CRUD;

use App\Models\Venue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

class VenueTest extends TestCase
{
    use RefreshDatabase;

    public function testAccessForGuests(): void
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        $this->get('/venues')
            ->assertRedirect('/login');

        $this->get('/venues/' . $venue->getId())
            ->assertRedirect('/login');

        $this->get('/venues/create')
            ->assertRedirect('/login');

        $this->post('/venues')
            ->assertRedirect('/login');

        $this->get('/venues/' . $venue->getId() . '/edit')
            ->assertRedirect('/login');

        $this->put('/venues/' . $venue->getId())
            ->assertRedirect('/login');

        $this->delete('/venues/' . $venue->getId())
            ->assertRedirect('/login');
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->make();

        $this->be(factory(User::class)->create());

        $this->get('/venues')
            ->assertOk();

        $this->get('/venues/create')
            ->assertOk();

        $this->post('/venues', $venue->toArray())
            ->assertRedirect('/venues');

        $venue = Venue::first();

        $this->get('/venues/' . $venue->getId())
            ->assertOk();

        $this->get('/venues/' . $venue->getId() . '/edit')
            ->assertOk();

        $this->put('/venues/' . $venue->getId(), $venue->toArray())
            ->assertRedirect('venues');

        $this->delete('/venues/' . $venue->getId())
            ->assertRedirect('venues');
    }

    public function testAccessForUnverifiedUsers(): void
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        $this->be(factory(User::class)->state('unverified')->create());

        $this->get('/venues')
            ->assertRedirect('/email/verify');

        $this->get('/venues/' . $venue->getId())
            ->assertRedirect('/email/verify');

        $this->get('/venues/create')
            ->assertRedirect('/email/verify');

        $this->post('/venues')
            ->assertRedirect('/email/verify');

        $this->get('/venues/' . $venue->getId() . '/edit')
            ->assertRedirect('/email/verify');

        $this->put('/venues/' . $venue->getId())
            ->assertRedirect('/email/verify');

        $this->delete('/venues/' . ($venue->getId()))
            ->assertRedirect('/email/verify');
    }

    public function testAddingAVenue(): void
    {
        $this->actingAs(factory(User::class)->create());

        // Missing required fields
        $this->post('/venues', [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseMissing('venues', ['name' => 'Olympic Stadium']);

        // OK
        $this->post('/venues', ['name' => 'Olympic Stadium'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('venues', ['name' => 'Olympic Stadium']);

        // Already existing venue
        $this->post('/venues', ['name' => 'Olympic Stadium'])
            ->assertSessionHasErrors('name', 'The venue already exists.');
        $this->assertDatabaseHas('venues', ['name' => 'Olympic Stadium']);
    }

    /**
     * @throws \Exception
     */
    public function testEditingAVenue(): void
    {
        $this->actingAs(factory(User::class)->create());

        $this->put('/venues/' . Uuid::generate()->string)
            ->assertNotFound();

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create(['name' => 'Olympic Stadium']);

        // Missing required fields
        $this->put('/venues/' . $venue->getId(), [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseHas('venues', ['name' => 'Olympic Stadium']);

        // OK
        $this->put('/venues/' . $venue->getId(), ['name' => 'Sobell S.C.'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('venues', ['name' => 'Olympic Stadium'])
            ->assertDatabaseHas('venues', ['name' => 'Sobell S.C.']);

        // Already existing venue
        factory(Venue::class)->create(['name' => 'University of Westminster']);
        $this->put('/venues/' . $venue->getId(), ['name' => 'University of Westminster'])
            ->assertSessionHasErrors('name', 'The venue already exists.');
        $this->assertDatabaseHas('venues', ['name' => 'Sobell S.C.']);
    }

    /**
     * @throws \Exception
     */
    public function testDeletingAVenue(): void
    {
        $this->actingAs(factory(User::class)->create());

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $club = aClub()->withVenue($venue)->build();

        $this->delete('/venues/' . $venue->getId())
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('venues', ['uuid' => $venue->getId()])
            ->assertDatabaseHas('clubs', ['id' => $club->getId(), 'venue_id' => null]);

        $this->delete('/venues/' . $venue->getId())
            ->assertNotFound();
    }
}
