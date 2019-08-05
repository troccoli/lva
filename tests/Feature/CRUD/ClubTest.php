<?php

namespace Tests\Feature\CRUD;

use App\Models\Club;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClubTest extends TestCase
{
    use RefreshDatabase;

    public function testAccessForGuests(): void
    {
        /** @var Club $club */
        $club = aClub()->build();

        $this->get('/clubs')
            ->assertRedirect('/login');

        $this->get('/clubs/create')
            ->assertRedirect('/login');

        $this->post('/clubs')
            ->assertRedirect('/login');

        $this->get('/clubs/' . $club->getId() . '/edit')
            ->assertRedirect('/login');

        $this->put('/clubs/' . $club->getId())
            ->assertRedirect('/login');

        $this->delete('/clubs/' . $club->getId())
            ->assertRedirect('/login');
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        /** @var Club $club */
        $club = aClub()->buildWithoutSaving();

        $this->be(factory(User::class)->create());

        $this->get('/clubs')
            ->assertOk();

        $this->get('/clubs/create')
            ->assertOk();

        $this->post('/clubs', $club->toArray())
            ->assertRedirect('/clubs');

        $club = Club::first();

        $this->get('/clubs/' . $club->getId() . '/edit')
            ->assertOk();

        $this->put('/clubs/' . $club->getId(), $club->toArray())
            ->assertRedirect('clubs');

        $this->delete('/clubs/' . $club->getId())
            ->assertRedirect('clubs');
    }

    public function testAccessForUnverifiedUsers(): void
    {
        /** @var Club $club */
        $club = aClub()->build();

        $this->be(factory(User::class)->state('unverified')->create());

        $this->get('/clubs')
            ->assertRedirect('/email/verify');

        $this->get('/clubs/create')
            ->assertRedirect('/email/verify');

        $this->post('/clubs')
            ->assertRedirect('/email/verify');

        $this->get('/clubs/' . $club->getId() . '/edit')
            ->assertRedirect('/email/verify');

        $this->put('/clubs/' . $club->getId())
            ->assertRedirect('/email/verify');

        $this->delete('/clubs/' . ($club->getId()))
            ->assertRedirect('/email/verify');
    }

    public function testAddingAClub(): void
    {
        $this->actingAs(factory(User::class)->create());

        // Missing required fields
        $this->post('/clubs', [])
            ->assertSessionHasErrors('name', 'The name is required.')
            ->assertSessionHasErrors('venue_id', 'The venue is required.');
        $this->assertDatabaseMissing('clubs', ['name' => 'London Giants']);

        // OK
        $this->post('/clubs', ['name' => 'London Giants', 'venue_id' => null])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('clubs', ['name' => 'London Giants', 'venue_id' => null]);
        $venue = factory(Venue::class)->create();
        $this->post('/clubs', ['name' => 'Global Warriors', 'venue_id' => $venue->getId()])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('clubs', ['name' => 'Global Warriors', 'venue_id' => $venue->getId()]);

        // Already existing club
        $this->post('/clubs', ['name' => 'London Giants'])
            ->assertSessionHasErrors('name', 'The club already exists.');
        $this->assertDatabaseHas('clubs', ['name' => 'London Giants']);

        // Non-existing venue
        $this->post('/clubs', ['name' => 'Sydenham Panters', 'venue_id' => 1])
            ->assertSessionHasErrors('venue_id', 'The venue does not exist.');
        $this->assertDatabaseMissing('clubs', ['name' => 'Sydenham Panters', 'venue_id' => null]);
    }

    public function testEditingAClub(): void
    {
        $this->actingAs(factory(User::class)->create());

        $this->put('/clubs/1')
            ->assertNotFound();

        /** @var Club $club */
        $club = aClub()->withName('Boston Scarlets')->build();
        $venueId = $club->getVenue()->getId();

        // Missing required fields
        $this->put('/clubs/' . $club->getId(), [])
            ->assertSessionHasErrors('name', 'The name is required.')
            ->assertSessionHasErrors('venue_id', 'The venue is required.');
        $this->assertDatabaseHas('clubs', ['name' => 'Boston Scarlets', 'venue_id' => $venueId]);

        // Wrong venue
        $this->put('/clubs/' . $club->getId(), ['name' => 'London Scarlets', 'venue_id' => 0])
            ->assertSessionHasErrors('venue_id', 'The venue does not exist.');
        $this->assertDatabaseHas('clubs', ['name' => 'Boston Scarlets', 'venue_id' => $venueId]);

        // OK
        $this->put('/clubs/' . $club->getId(), ['name' => 'London Scarlets', 'venue_id' => $venueId])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('clubs', ['name' => 'Boston Scarlets'])
            ->assertDatabaseHas('clubs', ['name' => 'London Scarlets', 'venue_id' => $venueId]);

        // Remove the venue
        $this->put('/clubs/' . $club->getId(), ['name' => 'London Scarlets', 'venue_id' => null])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('clubs', ['name' => 'London Scarlets', 'venue_id' => null]);

        aClub()->withName('London Giants')->build();

        // Club already exists
        $this->put('/clubs/' . $club->getId(), ['name' => 'London Giants', 'venue_id' => $venueId])
            ->assertSessionHasErrors('name', 'The club already exists.');
        $this->assertDatabaseHas('clubs', ['name' => 'London Scarlets', 'venue_id' => null]);
    }

    public function testDeletingAClub(): void
    {
        /** @var Club $club */
        $club = aClub()->build();

        $this->actingAs(factory(User::class)->create());

        $this->delete('/clubs/' . $club->getId())
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('clubs', ['id' => $club->getId()]);

        $this->delete('/clubs/' . $club->getId())
            ->assertNotFound();
    }
}
