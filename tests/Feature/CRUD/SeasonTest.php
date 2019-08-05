<?php

namespace Tests\Feature\CRUD;

use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeasonTest extends TestCase
{
    use RefreshDatabase;

    public function testAccessForGuests(): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->create();

        $this->get('/seasons')
            ->assertRedirect('/login');

        $this->get('/seasons/create')
            ->assertRedirect('/login');

        $this->post('/seasons')
            ->assertRedirect('/login');

        $this->get('/seasons/' . $season->getId() . '/edit')
            ->assertRedirect('/login');

        $this->put('/seasons/' . $season->getId())
            ->assertRedirect('/login');

        $this->delete('/seasons/' . $season->getId())
            ->assertRedirect('/login');
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->make();

        $this->be(factory(User::class)->create());

        $this->get('/seasons')
            ->assertOk();

        $this->get('/seasons/create')
            ->assertOk();

        $this->post('/seasons', $season->toArray())
            ->assertRedirect('/seasons');

        $season = Season::first();

        $this->get('/seasons/' . $season->getId() . '/edit')
            ->assertOk();

        $this->put('/seasons/' . $season->getId(), $season->toArray())
            ->assertRedirect('seasons');

        $this->delete('/seasons/' . $season->getId())
            ->assertRedirect('seasons');
    }

    public function testAccessForUnverifiedUsers(): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->create();

        $this->be(factory(User::class)->state('unverified')->create());

        $this->get('/seasons')
            ->assertRedirect('/email/verify');

        $this->get('/seasons/create')
            ->assertRedirect('/email/verify');

        $this->post('/seasons')
            ->assertRedirect('/email/verify');

        $this->get('/seasons/' . $season->getId() . '/edit')
            ->assertRedirect('/email/verify');

        $this->put('/seasons/' . $season->getId())
            ->assertRedirect('/email/verify');

        $this->delete('/seasons/' . ($season->getId()))
            ->assertRedirect('/email/verify');
    }

    public function testAddingASeason(): void
    {
        $this->actingAs(factory(User::class)->create());

        // Missing required fields
        $this->post('/seasons', [])
            ->assertSessionHasErrors('year', 'The year is required.');
        $this->assertDatabaseMissing('seasons', ['year' => '2000']);

        // Invalid year
        $this->post('/seasons', ['year' => 'Twothousand'])
            ->assertSessionHasErrors('year', 'The year is not valid.');
        $this->assertDatabaseMissing('seasons', ['year' => '2000']);

        // OK
        $this->post('/seasons', ['year' => '2000'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('seasons', ['year' => '2000']);

        // Already existing season
        $this->post('/seasons', ['year' => '2000'])
            ->assertSessionHasErrors('year', 'The season already exists.');
        $this->assertDatabaseHas('seasons', ['year' => '2000']);
    }

    public function testEditingASeason(): void
    {
        $this->actingAs(factory(User::class)->create());

        $this->put('/seasons/1')
            ->assertNotFound();

        /** @var Season $season */
        $season = factory(Season::class)->create(['year' => '2000']);

        // Missing required fields
        $this->put('/seasons/' . $season->getId(), [])
            ->assertSessionHasErrors('year', 'The year is required.');
        $this->assertDatabaseHas('seasons', ['year' => '2000']);

        // Invalid year
        $this->put('/seasons/' . $season->getId(), ['year' => 'Twothousand and one'])
            ->assertSessionHasErrors('year', 'The year is not valid.');
        $this->assertDatabaseHas('seasons', ['year' => '2000']);

        // OK
        $this->put('/seasons/' . $season->getId(), ['year' => '2001'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('seasons', ['year' => 2000])
            ->assertDatabaseHas('seasons', ['year' => '2001']);

        // Already existing season
        factory(Season::class)->create(['year' => '1999']);
        $this->put('/seasons/' . $season->getId(), ['year' => '1999'])
            ->assertSessionHasErrors('year', 'The season already exists.');
        $this->assertDatabaseHas('seasons', ['year' => '2001']);
    }

    public function testDeletingASeason(): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->create();

        $this->actingAs(factory(User::class)->create());

        $this->delete('/seasons/' . $season->getId())
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('seasons', ['id' => $season->getId()]);

        $this->delete('/seasons/' . $season->getId())
            ->assertNotFound();
    }
}
