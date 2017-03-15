<?php

namespace Tests\Models;

use LVA\Models\AvailableAppointment;
use LVA\Models\Role;
use Tests\Integration\IntegrationTestCase;

/**
 * Class RoleTest
 *
 * @package Tests\Models
 */
class RoleTest extends IntegrationTestCase
{
    /**
     * @test
     */
    public function it_gets_the_id()
    {
        $role = factory(Role::class)->create();

        $this->assertEquals($role->id, $role->getId());
    }

    /**
     * @test
     */
    public function it_is_a_string()
    {
        $role = factory(Role::class)->create();

        $this->assertEquals($role->role, (string)$role);
    }

    /**
     * @test
     */
    public function it_has_many_appointments()
    {
        // random number of appointments to create
        $appointments = mt_rand(2,10);

        /** @var Role[] $roles */
        $roles = factory(Role::class)->times(2)->create();

        // Create the appointments for the first role;
        factory(AvailableAppointment::class)->times($appointments)->create(['role_id' => $roles[0]->id]);

        $this->assertCount(0, $roles[1]->available_appointments);
        $this->assertCount($appointments, $roles[0]->available_appointments);
    }
}
