<?php

namespace Tests\Feature\CRUD;

use App\Events\ClubCreated;
use App\Models\Club;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
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

    public function testAccessForUserWithoutThePermission(): void
    {
        /** @var Club $club */
        $club = aClub()->build();

        $this->actingAs(factory(User::class)->create());

        $this->get('/clubs')
            ->assertForbidden();

        $this->get('/clubs/create')
            ->assertForbidden();

        $this->post('/clubs', $club->toArray())
            ->assertForbidden();

        $club = Club::first();

        $this->get('/clubs/' . $club->getId() . '/edit')
            ->assertForbidden();

        $this->put('/clubs/' . $club->getId(), $club->toArray())
            ->assertForbidden();

        $this->delete('/clubs/' . $club->getId())
            ->assertForbidden();
    }

    public function testAccessForSuperAdmin(): void
    {
        /** @var Club $club */
        $club = aClub()->buildWithoutSaving();

        $this->actingAs(factory(User::class)->create()->assignRole('Site Administrator'));

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

        $this->actingAs(factory(User::class)->state('unverified')->create());

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
        $this->actingAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

        $this->post('/clubs', [])
            ->assertSessionHasErrors('name', 'The name is required.')
            ->assertSessionHasErrors('venue_id', 'The venue is required.');
        $this->assertDatabaseMissing('clubs', ['name' => 'London Giants']);

        $this->post('/clubs', ['name' => 'London Giants', 'venue_id' => null])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('clubs', ['name' => 'London Giants', 'venue_id' => null]);

        $this->post('/clubs', ['name' => 'London Giants'])
            ->assertSessionHasErrors('name', 'The club already exists.');
        $this->assertDatabaseHas('clubs', ['name' => 'London Giants']);

        $this->post('/clubs', ['name' => 'Global Warriors', 'venue_id' => 1])
            ->assertSessionHasErrors('venue_id', 'The venue does not exist.');
        $this->assertDatabaseMissing('clubs', ['name' => 'Global Warriors', 'venue_id' => null]);

        $venue = factory(Venue::class)->create();
        $this->post('/clubs', ['name' => 'Global Warriors', 'venue_id' => $venue->getId()])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('clubs', ['name' => 'Global Warriors', 'venue_id' => $venue->getId()]);
    }

    public function testEditingAClub(): void
    {
        $this->actingAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

        $this->put('/clubs/1')
            ->assertNotFound();

        /** @var Club $club */
        $club = aClub()->withName('Boston Scarlets')->build();
        $venueId = $club->getVenue()->getId();

        $this->put('/clubs/' . $club->getId(), [])
            ->assertSessionHasErrors('name', 'The name is required.')
            ->assertSessionHasErrors('venue_id', 'The venue is required.');
        $this->assertDatabaseHas('clubs', ['name' => 'Boston Scarlets', 'venue_id' => $venueId]);

        $this->put('/clubs/' . $club->getId(), ['name' => 'Boston Former Scarlets', 'venue_id' => 0])
            ->assertSessionHasErrors('venue_id', 'The venue does not exist.');
        $this->assertDatabaseHas('clubs', ['name' => 'Boston Scarlets', 'venue_id' => $venueId]);

        $this->put('/clubs/' . $club->getId(), ['name' => 'Boston Former Scarlets', 'venue_id' => $venueId])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('clubs', ['name' => 'Boston Scarlets'])
            ->assertDatabaseHas('clubs', ['name' => 'Boston Former Scarlets', 'venue_id' => $venueId]);

        aClub()->withName('London Giants')->build();

        $this->put('/clubs/' . $club->getId(), ['name' => 'London Giants', 'venue_id' => $venueId])
            ->assertSessionHasErrors('name', 'The club already exists.');
        $this->assertDatabaseHas('clubs', ['name' => 'Boston Former Scarlets', 'venue_id' => $venueId]);

        $newVenueId = factory(Venue::class)->create()->getId();

        $this->put('/clubs/' . $club->getId(), ['name' => 'Boston Former Scarlets', 'venue_id' => $newVenueId])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('clubs', ['name' => 'Boston Former Scarlets', 'venue_id' => $newVenueId]);

        $this->put('/clubs/' . $club->getId(), ['name' => 'Boston Former Scarlets', 'venue_id' => null])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('clubs', ['name' => 'Boston Former Scarlets', 'venue_id' => null]);
    }

    public function testDeletingAClub(): void
    {
        $this->actingAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

        $this->delete('/clubs/1')
            ->assertNotFound();

        /** @var Club $club */
        $club = aClub()->build();

        $this->delete('/clubs/' . $club->getId())
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('clubs', ['id' => $club->getId()]);
    }

    public function testAddingClubWillDispatchTheEvent(): void
    {
        Event::fake();

        $this->actingAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

        $this->post('/clubs', ['name' => 'London Giants', 'venue_id' => null]);

        Event::assertDispatched(ClubCreated::class);
    }
}
