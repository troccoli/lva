<?php

namespace Tests\Unit\Models;

use LVA\Models\AvailableAppointment;
use LVA\Models\Fixture;
use LVA\Models\Role;
use Tests\TestCase;

/**
 * Class AvailableAppointmentTest.
 */
class AvailableAppointmentTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_one_fixture()
    {
        /** @var AvailableAppointment $appointment */
        $appointment = factory(AvailableAppointment::class)->create();

        $this->assertInstanceOf(Fixture::class, $appointment->fixture);
    }

    /**
     * @test
     */
    public function it_has_one_role()
    {
        /** @var AvailableAppointment $appointment */
        $appointment = factory(AvailableAppointment::class)->create();

        $this->assertInstanceOf(Role::class, $appointment->role);
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var AvailableAppointment $appointment */
        $appointment = factory(AvailableAppointment::class)->create();

        $this->assertEquals($appointment->id, $appointment->getId());
    }
}
