<?php

namespace Tests\Integration\Api\V1;

use App\Helpers\RolesHelper;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FixturesTest extends TestCase
{
    use TestFixtures;

    public function testFixtures(): void
    {
        foreach ($this->allRoles() as $role) {
            Passport::actingAs($this->userWithRole($role));

            $this->checkForInvalidParameters();
            $this->checkPagination();
            $this->checkForCorrectFixtures();
        }
    }

    public function checkForInvalidParameters(): void
    {
        /* non-existing season */
        $this->getJson('/api/v1/fixtures?season=100')
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        /* non-existing competition */
        $this->getJson('/api/v1/fixtures?competition=100')
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        /* non-existing division */
        $this->getJson('/api/v1/fixtures?division=100')
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        /* wrong date */
        $this->getJson('/api/v1/fixtures?on=20200101')
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->getJson('/api/v1/fixtures?on=2020/01/01')
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->getJson('/api/v1/fixtures?on=01-01-2020')
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->getJson('/api/v1/fixtures?on=01/01/2020')
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->getJson('/api/v1/fixtures?on=2020-31-12')
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        /* non-existing venue */
        $this->getJson('/api/v1/fixtures?at=100')
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        /* non-existing team */
        $this->getJson('/api/v1/fixtures?team=100')
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->getJson('/api/v1/fixtures?homeTeam=100')
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->getJson('/api/v1/fixtures?awayTeam=100')
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function checkPagination(): void
    {
        $this->getJson('/api/v1/fixtures?perPage=15')
             ->assertJsonCount(15, 'data')
             ->assertJsonPath('meta.current_page', 1)
             ->assertJsonPath('meta.last_page', 3)
             ->assertJsonPath('meta.from', 1)
             ->assertJsonPath('meta.to', 15)
             ->assertJsonPath('meta.per_page', 15)
             ->assertJsonPath('meta.total', 34);
        $this->getJson('/api/v1/fixtures?perPage=15&page=2')
             ->assertJsonCount(15, 'data')
             ->assertJsonPath('meta.current_page', 2)
             ->assertJsonPath('meta.last_page', 3)
             ->assertJsonPath('meta.from', 16)
             ->assertJsonPath('meta.to', 30)
             ->assertJsonPath('meta.per_page', 15)
             ->assertJsonPath('meta.total', 34);
        $this->getJson('/api/v1/fixtures?perPage=15&page=3')
             ->assertJsonCount(4, 'data')
             ->assertJsonPath('meta.current_page', 3)
             ->assertJsonPath('meta.last_page', 3)
             ->assertJsonPath('meta.from', 31)
             ->assertJsonPath('meta.to', 34)
             ->assertJsonPath('meta.per_page', 15)
             ->assertJsonPath('meta.total', 34);

        $this->getJson('/api/v1/fixtures?page=1')
             ->assertJsonCount(10, 'data')
             ->assertJsonPath('meta.current_page', 1)
             ->assertJsonPath('meta.last_page', 4)
             ->assertJsonPath('meta.from', 1)
             ->assertJsonPath('meta.to', 10)
             ->assertJsonPath('meta.per_page', 10)
             ->assertJsonPath('meta.total', 34);
        $this->getJson('/api/v1/fixtures?page=2')
             ->assertJsonCount(10, 'data')
             ->assertJsonPath('meta.current_page', 2)
             ->assertJsonPath('meta.last_page', 4)
             ->assertJsonPath('meta.from', 11)
             ->assertJsonPath('meta.to', 20)
             ->assertJsonPath('meta.per_page', 10)
             ->assertJsonPath('meta.total', 34);
        $this->getJson('/api/v1/fixtures?page=3')
             ->assertJsonCount(10, 'data')
             ->assertJsonPath('meta.current_page', 3)
             ->assertJsonPath('meta.last_page', 4)
             ->assertJsonPath('meta.from', 21)
             ->assertJsonPath('meta.to', 30)
             ->assertJsonPath('meta.per_page', 10)
             ->assertJsonPath('meta.total', 34);
        $this->getJson('/api/v1/fixtures?page=4')
             ->assertJsonCount(4, 'data')
             ->assertJsonPath('meta.current_page', 4)
             ->assertJsonPath('meta.last_page', 4)
             ->assertJsonPath('meta.from', 31)
             ->assertJsonPath('meta.to', 34)
             ->assertJsonPath('meta.per_page', 10)
             ->assertJsonPath('meta.total', 34);
    }

    public function checkForCorrectFixtures(): void
    {
        $this->getJson('/api/v1/fixtures')
             ->assertJsonCount(34, 'data');

        $this->getJson("/api/v1/fixtures?season={$this->season1->getId()}")
             ->assertJsonCount(24, 'data');
        $this->getJson("/api/v1/fixtures?season={$this->season2->getId()}")
             ->assertJsonCount(10, 'data');

        $this->getJson("/api/v1/fixtures?competition={$this->competition1->getId()}")
             ->assertJsonCount(12, 'data');
        $this->getJson("/api/v1/fixtures?competition={$this->competition2->getId()}")
             ->assertJsonCount(12, 'data');
        $this->getJson("/api/v1/fixtures?competition={$this->competition3->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?competition={$this->competition4->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?competition={$this->competition5->getId()}")
             ->assertJsonCount(2, 'data');

        $this->getJson("/api/v1/fixtures?division={$this->division1->getId()}")
             ->assertJsonCount(12, 'data');
        $this->getJson("/api/v1/fixtures?division={$this->division2->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?division={$this->division3->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?division={$this->division4->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?division={$this->division5->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?division={$this->division6->getId()}")
             ->assertJsonCount(2, 'data');

        $this->getJson('/api/v1/fixtures?on=2000-08-16')
             ->assertJsonCount(4, 'data');
        $this->getJson('/api/v1/fixtures?on=2000-08-17')
             ->assertJsonCount(4, 'data');
        $this->getJson('/api/v1/fixtures?on=2000-08-18')
             ->assertJsonCount(4, 'data');
        $this->getJson('/api/v1/fixtures?on=2000-08-19')
             ->assertJsonCount(4, 'data');
        $this->getJson('/api/v1/fixtures?on=2000-08-20')
             ->assertJsonCount(4, 'data');
        $this->getJson('/api/v1/fixtures?on=2000-08-21')
             ->assertJsonCount(4, 'data');
        $this->getJson('/api/v1/fixtures?on=2001-08-16')
             ->assertJsonCount(3, 'data');
        $this->getJson('/api/v1/fixtures?on=2001-08-17')
             ->assertJsonCount(3, 'data');
        $this->getJson('/api/v1/fixtures?on=2001-08-18')
             ->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/fixtures?on=2001-08-19')
             ->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/fixtures?on=2001-08-20')
             ->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/fixtures?on=2001-08-21')
             ->assertJsonCount(1, 'data');

        $this->getJson("/api/v1/fixtures?team={$this->team1->getId()}")
             ->assertJsonCount(14, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team2->getId()}")
             ->assertJsonCount(14, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team3->getId()}")
             ->assertJsonCount(14, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team4->getId()}")
             ->assertJsonCount(12, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team5->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team6->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team7->getId()}")
             ->assertJsonCount(2, 'data');

        $this->getJson("/api/v1/fixtures?homeTeam={$this->team1->getId()}")
             ->assertJsonCount(7, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team2->getId()}")
             ->assertJsonCount(7, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team3->getId()}")
             ->assertJsonCount(7, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team4->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team5->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team6->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team7->getId()}")
             ->assertJsonCount(1, 'data');

        $this->getJson("/api/v1/fixtures?awayTeam={$this->team1->getId()}")
             ->assertJsonCount(7, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team2->getId()}")
             ->assertJsonCount(7, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team3->getId()}")
             ->assertJsonCount(7, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team4->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team5->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team6->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team7->getId()}")
             ->assertJsonCount(1, 'data');

        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}")
             ->assertJsonCount(13, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}")
             ->assertJsonCount(8, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}")
             ->assertJsonCount(13, 'data');

        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&on=2000-08-16")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&on=2000-08-16")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&on=2000-08-16")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&on=2000-08-17")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&on=2000-08-17")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&on=2000-08-17")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&on=2000-08-18")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&on=2000-08-18")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&on=2000-08-18")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&on=2000-08-19")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&on=2000-08-19")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&on=2000-08-19")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&on=2000-08-20")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&on=2000-08-20")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&on=2000-08-20")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&on=2000-08-21")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&on=2000-08-21")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&on=2000-08-21")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&on=2001-08-16")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&on=2001-08-16")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&on=2001-08-16")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&on=2001-08-17")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&on=2001-08-17")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&on=2001-08-17")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&on=2001-08-18")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&on=2001-08-18")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&on=2001-08-18")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&on=2001-08-19")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&on=2001-08-19")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&on=2001-08-19")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&on=2001-08-20")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&on=2001-08-20")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&on=2001-08-20")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&on=2001-08-21")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&on=2001-08-21")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&on=2001-08-21")
             ->assertJsonCount(0, 'data');

        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&competition={$this->competition1->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&competition={$this->competition1->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&competition={$this->competition1->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&competition={$this->competition2->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&competition={$this->competition2->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&competition={$this->competition2->getId()}")
             ->assertJsonCount(4, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&competition={$this->competition3->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&competition={$this->competition3->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&competition={$this->competition3->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&competition={$this->competition4->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&competition={$this->competition4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&competition={$this->competition4->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&competition={$this->competition5->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&competition={$this->competition5->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&competition={$this->competition5->getId()}")
             ->assertJsonCount(0, 'data');

        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(4, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(4, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue1->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue2->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?at={$this->venue3->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');

        $this->getJson("/api/v1/fixtures?team={$this->team1->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team2->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team3->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team4->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(6, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team5->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team6->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team7->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team1->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(4, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team2->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(4, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team3->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team4->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team5->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(4, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team6->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team7->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team1->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team2->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team3->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(4, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team4->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(4, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team5->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team6->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(4, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team7->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team1->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(4, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team2->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(4, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team3->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(4, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team4->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team5->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team6->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team7->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team1->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team2->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team3->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team4->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team5->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team6->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team7->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team1->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team2->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team3->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team4->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team5->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team6->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?team={$this->team7->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(2, 'data');

        $this->getJson("/api/v1/fixtures?homeTeam={$this->team1->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team2->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team3->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team4->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team5->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team6->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team7->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team1->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team2->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team3->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team4->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team5->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team6->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team7->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team1->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team2->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team3->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team4->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team5->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team6->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team7->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team1->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team2->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team3->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team4->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team5->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team6->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team7->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team1->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team2->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team3->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team4->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team5->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team6->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team7->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team1->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team2->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team3->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team4->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team5->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team6->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?homeTeam={$this->team7->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(1, 'data');

        $this->getJson("/api/v1/fixtures?awayTeam={$this->team1->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team2->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team3->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team4->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(3, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team5->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team6->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team7->getId()}&division={$this->division1->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team1->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team2->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team3->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team4->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team5->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team6->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team7->getId()}&division={$this->division2->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team1->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team2->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team3->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team4->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team5->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team6->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team7->getId()}&division={$this->division3->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team1->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team2->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team3->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team4->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team5->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team6->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team7->getId()}&division={$this->division4->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team1->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team2->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team3->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team4->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team5->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team6->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team7->getId()}&division={$this->division5->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team1->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team2->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team3->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team4->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team5->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team6->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/fixtures?awayTeam={$this->team7->getId()}&division={$this->division6->getId()}")
             ->assertJsonCount(1, 'data');
    }

    public function allRoles(): array
    {
        return [
            'Site Administrator' => RolesHelper::SITE_ADMIN,
            'Season 1 Administrator' => RolesHelper::seasonAdmin($this->season1),
            'Season 2 Administrator' => RolesHelper::seasonAdmin($this->season2),
            'Competition 1 Administrator' => RolesHelper::competitionAdmin($this->competition1),
            'Competition 2 Administrator' => RolesHelper::competitionAdmin($this->competition2),
            'Competition 3 Administrator' => RolesHelper::competitionAdmin($this->competition3),
            'Competition 4 Administrator' => RolesHelper::competitionAdmin($this->competition4),
            'Competition 5 Administrator' => RolesHelper::competitionAdmin($this->competition5),
            'Division 1 Administrator' => RolesHelper::divisionAdmin($this->division1),
            'Division 2 Administrator' => RolesHelper::divisionAdmin($this->division2),
            'Division 3 Administrator' => RolesHelper::divisionAdmin($this->division3),
            'Division 4 Administrator' => RolesHelper::divisionAdmin($this->division4),
            'Division 5 Administrator' => RolesHelper::divisionAdmin($this->division5),
            'Division 6 Administrator' => RolesHelper::divisionAdmin($this->division6),
            'Club 1 Secretary' => RolesHelper::clubSecretary($this->club1),
            'Club 2 Secretary' => RolesHelper::clubSecretary($this->club2),
            'Club 3 Secretary' => RolesHelper::clubSecretary($this->club3),
            'Club 4 Secretary' => RolesHelper::clubSecretary($this->club4),
            'Team 1 Secretary' => RolesHelper::teamSecretary($this->team1),
            'Team 2 Secretary' => RolesHelper::teamSecretary($this->team2),
            'Team 3 Secretary' => RolesHelper::teamSecretary($this->team3),
            'Team 4 Secretary' => RolesHelper::teamSecretary($this->team4),
            'Team 5 Secretary' => RolesHelper::teamSecretary($this->team5),
            'Team 6 Secretary' => RolesHelper::teamSecretary($this->team6),
            'Team 7 Secretary' => RolesHelper::teamSecretary($this->team7),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ThrottleRequests::class);

        $this->setUpFixtures();
    }
}
