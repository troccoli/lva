<?php

namespace Tests\Models;

use LVA\Models\Fixture;
use LVA\Models\Role;
use LVA\Models\AvailableAppointment;
use Tests\OldStyleTestCase;

/**
 * Class AvailableAppointmentTest
 *
 * @package Tests\Models
 */
class AvailableAppointmentOldStyleTest extends OldStyleTestCase
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
