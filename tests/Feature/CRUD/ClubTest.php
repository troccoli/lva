<?php

namespace Tests\Feature\CRUD;

use App\Models\Club;
use App\Models\User;
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

        $this->post('/clubs', [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseMissing('clubs', ['name' => 'London Giants']);

        $this->post('/clubs', ['name' => 'London Giants'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('clubs', ['name' => 'London Giants']);

        $this->post('/clubs', ['name' => 'London Giants'])
            ->assertSessionHasErrors('name', 'The club already exists.');
        $this->assertDatabaseHas('clubs', ['name' => 'London Giants']);
    }

    public function testEditingAClub(): void
    {
        $this->actingAs(factory(User::class)->create());

        $this->put('/clubs/1')
            ->assertNotFound();

        /** @var Club $club */
        $club = aClub()->withName('Boston Scarlets')->build();

        $this->put('/clubs/' . $club->getId(), [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseHas('clubs', ['name' => 'Boston Scarlets']);

        $this->put('/clubs/' . $club->getId(), ['name' => 'Boston Former Scarlets'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('clubs', ['name' => 'Boston Scarlets'])
            ->assertDatabaseHas('clubs', ['name' => 'Boston Former Scarlets']);

        aClub()->withName('London Giants')->build();

        $this->put('/clubs/' . $club->getId(), ['name' => 'London Giants'])
            ->assertSessionHasErrors('name', 'The club already exists.');
        $this->assertDatabaseHas('clubs', ['name' => 'Boston Former Scarlets']);
    }

    public function testDeletingAClub(): void
    {
        $this->actingAs(factory(User::class)->create());

        $this->delete('/clubs/1')
            ->assertNotFound();

        /** @var Club $club */
        $club = aClub()->build();

        $this->delete('/clubs/' . $club->getId())
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('clubs', ['id' => $club->getId()]);
    }
}
