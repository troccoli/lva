<?php

namespace Tests\Integration\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use LVA\Models\AvailableAppointment;
use LVA\Models\Division;
use LVA\Models\Fixture;
use LVA\Models\Team;
use LVA\Models\Venue;
use Tests\TestCase;

/**
 * Class Fixtures
 *
 * @package Tests\Integration\Models
 */
class Fixtures extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_belongs_to_a_division()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $this->assertInstanceOf(Division::class, $fixture->division);
    }

    /**
     * @test
     */
    public function it_has_a_home_team()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $this->assertInstanceOf(Team::class, $fixture->home_team);
    }

    /**
     * @test
     */
    public function it_has_an_away_team()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $this->assertInstanceOf(Team::class, $fixture->away_team);
    }

    /**
     * @test
     */
    public function it_has_a_venue()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $this->assertInstanceOf(Venue::class, $fixture->venue);

    }

    /**
     * @test
     */
    public function it_has_many_available_appointments()
    {
        // Random number of available appointments
        $appointmens = mt_rand(2, 10);

        /** @var Fixture[] $fixtures */
        $fixtures = factory(Fixture::class)->times(2)->create();

        // create a random number of available appointments for the first fixture
        factory(AvailableAppointment::class)->times($appointmens)->create(['fixture_id' => $fixtures[0]->getId()]);

        $this->assertCount(0, $fixtures[1]->available_appointments);
        $this->assertCount($appointmens, $fixtures[0]->available_appointments);
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $this->assertEquals($fixture->id, $fixture->getId());
    }

    /**
     * @test
     */
    public function it_can_set_the_division_id()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        /** @var Division $newDivision */
        $newDivision = factory(Division::class)->create();

        $this->assertNotEquals($newDivision->getId(), $fixture->division_id);

        $fixture->setDivision($newDivision->getId());
        $this->assertEquals($newDivision->getId(), $fixture->division_id);
    }

    /**
     * @test
     */
    public function it_can_set_the_match_number()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $newMatchNumber = $fixture->match_number + 1;

        $this->assertNotEquals($newMatchNumber, $fixture->match_number);

        $fixture->setMatchNumber($newMatchNumber);
        $this->assertEquals($newMatchNumber, $fixture->match_number);
    }

    /**
     * @test
     */
    public function it_can_set_the_match_date()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        /** @var Carbon $newDate */
        $newDate = $fixture->match_date->addDay();

        $this->assertNotEquals($newDate->format('Y-m-d'), $fixture->match_date->format('Y-m-d'));

        $fixture->setMatchDate($newDate);
        $this->assertEquals($newDate->format('Y-m-d'), $fixture->match_date->format('Y-m-d'));
    }

    /**
     * @test
     */
    public function it_can_set_the_warm_up_time()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        /** @var Carbon $newWarmUpTime */
        $newWarmUpTime = $fixture->warm_up_time->addHour();

        $this->assertNotEquals($newWarmUpTime->format('H:i'), $fixture->warm_up_time->format('H:i'));

        $fixture->setWarmUpTime($newWarmUpTime);
        $this->assertEquals($newWarmUpTime->format('H:i'), $fixture->warm_up_time->format('H:i'));
    }

    /**
     * @test
     */
    public function it_can_set_the_start_time()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        /** @var Carbon $newStartTime */
        $newStartTime = $fixture->start_time->addHour();

        $this->assertNotEquals($newStartTime->format('H:i'), $fixture->start_time->format('H:i'));

        $fixture->setStartTime($newStartTime);
        $this->assertEquals($newStartTime->format('H:i'), $fixture->start_time->format('H:i'));
    }

    /**
     * @test
     */
    public function it_can_set_the_home_team()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        /** @var Team $newHomeTeam */
        $newHomeTeam = factory(Team::class)->create();

        $this->assertNotEquals($newHomeTeam->getId(), $fixture->home_team_id);

        $fixture->setHomeTeam($newHomeTeam->getId());
        $this->assertEquals($newHomeTeam->getId(), $fixture->home_team_id);
    }

    /**
     * @test
     */
    public function it_can_set_the_away_team()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        /** @var Team $newAwayTeam */
        $newAwayTeam = factory(Team::class)->create();

        $this->assertNotEquals($newAwayTeam->getId(), $fixture->away_team_id);

        $fixture->setAwayTeam($newAwayTeam->getId());
        $this->assertEquals($newAwayTeam->getId(), $fixture->away_team_id);
    }

    /**
     * @test
     */
    public function it_can_set_the_venue()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        /** @var Venue $newVenue */
        $newVenue = factory(Team::class)->create();

        $this->assertNotEquals($newVenue->getId(), $fixture->venue_id);

        $fixture->setVenue($newVenue->getId());
        $this->assertEquals($newVenue->getId(), $fixture->venue_id);
    }

    /**
     * @test
     */
    public function it_returns_the_warm_up_time_as_Carbon()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $this->assertInstanceOf(Carbon::class, $fixture->warm_up_time);
    }

    /**
     * @test
     */
    public function it_returns_the_start_time_as_Carbon()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $this->assertInstanceOf(Carbon::class, $fixture->start_time);
    }

    /**
     * @test
     */
    public function it_returns_the_match_date_as_Carbon()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $this->assertInstanceOf(Carbon::class, $fixture->match_date);
    }

    /**
     * @test
     */
    public function it_gets_the_notes()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $this->assertEquals($fixture->notes, $fixture->getNotes());
    }

    /**
     * @test
     */
    public function it_is_a_string()
    {
        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $string = $fixture->division . ':' . $fixture->match_number . ' ' .
            $fixture->match_date->format('d/m/y') . ' ' .
            $fixture->start_time->format('H:i') . '(' . $fixture->warm_up_time->format('H:i') . ') ' .
            $fixture->home_team . ' v ' . $fixture->away_team;

        $this->assertEquals($string, (string)$fixture);
    }

}
