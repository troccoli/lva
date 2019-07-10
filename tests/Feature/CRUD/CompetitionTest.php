<?php

namespace Tests\Feature\CRUD;

use App\Models\Competition;
use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionTest extends TestCase
{
    use RefreshDatabase;

    public function testAccessForGuests(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();

        $this->get('/competitions')
            ->assertRedirect('/login');

        $this->get('/competitions/create')
            ->assertRedirect('/login');

        $this->post('/competitions')
            ->assertRedirect('/login');

        $this->get('/competitions/' . $competition->getId() . '/edit')
            ->assertRedirect('/login');

        $this->put('/competitions/' . $competition->getId())
            ->assertRedirect('/login');

        $this->delete('/competitions/' . $competition->getId())
            ->assertRedirect('/login');
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->make();

        $this->be(factory(User::class)->create());

        $this->get('/competitions')
            ->assertOk();

        $this->get('/competitions/create')
            ->assertOk();

        $this->post('/competitions', $competition->toArray())
            ->assertRedirect('/competitions?season_id=' . $competition->getSeason()->getId());

        $competition = Competition::first();

        $this->get('/competitions/' . $competition->getId() . '/edit')
            ->assertOk();

        $this->put('/competitions/' . $competition->getId(), $competition->toArray())
            ->assertRedirect('/competitions?season_id=' . $competition->getSeason()->getId());

        $this->delete('/competitions/' . $competition->getId())
            ->assertRedirect('/competitions?season_id=' . $competition->getSeason()->getId());
    }

    public function testAccessForUnverifiedUsers(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();

        $this->be(factory(User::class)->state('unverified')->create());

        $this->get('/competitions')
            ->assertRedirect('/email/verify');

        $this->get('/competitions/create')
            ->assertRedirect('/email/verify');

        $this->post('/competitions')
            ->assertRedirect('/email/verify');

        $this->get('/competitions/' . $competition->getId() . '/edit')
            ->assertRedirect('/email/verify');

        $this->put('/competitions/' . $competition->getId())
            ->assertRedirect('/email/verify');

        $this->delete('/competitions/' . ($competition->getId()))
            ->assertRedirect('/email/verify');
    }

    public function testAddingACompetition(): void
    {
        $this->actingAs(factory(User::class)->create());

        $this->post('/competitions', [])
            ->assertSessionHasErrors('season_id', 'The season is required.')
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseMissing('competitions', ['name' => 'London League - Men']);

        $this->post('/competitions', ['name' => 'London League - Men'])
            ->assertSessionHasErrors('season_id', 'The season is required.');
        $this->assertDatabaseMissing('competitions', ['name' => 'London League - Men']);

        $this->post('/competitions', ['season_id' => 1, 'name' => 'London League - Men'])
            ->assertSessionHasErrors('season_id', 'The season does not exist.');
        $this->assertDatabaseMissing('competitions', ['name' => 'London League - Men']);

        $season = factory(Season::class)->create();
        $this->post('/competitions', ['season_id' => $season->getId(), 'name' => 'London League - Men'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competitions', ['name' => 'London League - Men']);

        $this->post('/competitions', ['season_id' => $season->getId(), 'name' => 'London League - Men'])
            ->assertSessionHasErrors('name', 'The competition already exists.');
        $this->assertDatabaseHas('competitions', ['name' => 'London League - Men']);
    }

    public function testEditingACompetition(): void
    {
        $this->put('/competitions/1')
            ->assertRedirect();

        /** @var Competition $competition */
        $competition = factory(Competition::class)->create(['name' => 'London League - Men']);
        $seasonId = $competition->getSeason()->getId();

        $this->actingAs(factory(User::class)->create());

        $this->put('/competitions/' . $competition->getId(), [])
            ->assertSessionHasErrors('name', 'The name is required.');
        $this->assertDatabaseHas('competitions', ['season_id' => $seasonId, 'name' => 'London League - Men']);

        $this->put('/competitions/' . $competition->getId(), ['name' => 'London League - Women'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competitions', ['season_id' => $seasonId, 'name' => 'London League - Women']);

        factory(Competition::class)->create([
            'season_id' => $seasonId,
            'name'      => 'University League',
        ]);

        $this->put('/competitions/' . $competition->getId(), ['name' => 'University League'])
            ->assertSessionHasErrors('name', 'The competition already exists in this season.');
        $this->assertDatabaseHas('competitions', ['season_id' => $seasonId, 'name' => 'London League - Women']);

        $wp = factory(Competition::class)->create(['name' => 'Youth Games']);

        $this->put('/competitions/' . $competition->getId(), ['name' => 'Youth Games'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competitions', ['season_id' => $seasonId, 'name' => 'Youth Games'])
            ->assertDatabaseHas('competitions', ['season_id' => $wp->getSeason()->getId(), 'name' => 'Youth Games']);
    }

    public function testDeletingACompetition(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create(['name' => 'London League - Men']);

        $this->actingAs(factory(User::class)->create());

        $this->delete('/competitions/' . $competition->getId())
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('competitions', ['name' => 'London League - Men']);

        $this->delete('/competitions/' . ($competition->getId() + 1))
            ->assertNotFound();
    }
}
