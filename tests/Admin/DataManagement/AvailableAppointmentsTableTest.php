<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use Tests\TestCase;
use App\Models\AvailableAppointment;
use App\Models\Fixture;
use App\Models\Role;

class AvailableAppointmentsTableTest extends TestCase
{
    const BASE_ROUTE = 'admin.data-management.available-appointments';

    public function testRedirectIfNotAdmin()
    {
        $this->visit(route(self::BASE_ROUTE . '.index'))
            ->seePageIs(route('login'));

        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->seePageIs(route('login'));

        $this->visit(route(self::BASE_ROUTE . '.show', [1]))
            ->seePageIs(route('login'));

        $this->visit(route(self::BASE_ROUTE . '.edit', [1]))
            ->seePageIs(route('login'));

        $this->call('POST', route(self::BASE_ROUTE . '.store'));
        $this->assertResponseStatus(302);

        $this->call('DELETE', route(self::BASE_ROUTE . '.destroy', [1]));
        $this->assertResponseStatus(302);

        $this->call('PUT', route(self::BASE_ROUTE . '.update', [1]));
        $this->assertResponseStatus(302);
    }

    public function testBreadcrumbs()
    {
        $this->be($this->getFakeUser());
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Available appointments');
    }

    public function testAddAvailableAppointment()
    {
        $this->be($this->getFakeUser());

        /** @var AvailableAppointment $appointment */
        $appointment = factory(AvailableAppointment::class)->make();

        /** @var Fixture $newFixture */
        $newFixture = factory(Fixture::class)->create();
        /** @var Role $newRole */
        $newRole = factory(Role::class)->create();

        // Brand new appointment
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($appointment->fixture_id, 'fixture_id')
            ->select($appointment->role_id, 'role_id')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Appointment added!')
            ->seeInDatabase('available_appointments', [
                'id'         => 1,
                'fixture_id' => $appointment->fixture_id,
                'role_id'    => $appointment->role_id,
            ]);

        // Already existing appointment in the same fixture with different role
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($appointment->fixture_id, 'fixture_id')
            ->select($newRole->id, 'role_id')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Appointment added!')
            ->seeInDatabase('available_appointments', [
                'id'         => 2,
                'fixture_id' => $appointment->fixture_id,
                'role_id'    => $newRole->id,
            ]);

        // Already existing appointment in the same role with different fixture
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($newFixture->id, 'fixture_id')
            ->select($appointment->role_id, 'role_id')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Appointment added!')
            ->seeInDatabase('available_appointments', [
                'id'         => 3,
                'fixture_id' => $newFixture->id,
                'role_id'    => $appointment->role_id,
            ]);

        // Already existing appointment
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($appointment->fixture_id, 'fixture_id')
            ->select($appointment->role_id, 'role_id')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'Appointment already added.')
            ->dontSeeInDatabase('available_appointments', ['id' => 4]);
    }

    public function testEditAvailableAppointment()
    {
        $this->be($this->getFakeUser());

        /** @var AvailableAppointment $appointment */
        $appointment = factory(AvailableAppointment::class)->create();

        /** @var Fixture $newFixture */
        $newFixture = factory(Fixture::class)->create();
        /** @var Role $newRole */
        $newRole = factory(Role::class)->create();

        // Change the role of the existing appointment
        $this->seeInDatabase('available_appointments', [
            'id'         => $appointment->id,
            'fixture_id' => $appointment->fixture_id,
            'role_id'    => $appointment->role_id,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$appointment->id]))
            ->select($newRole->id, 'role_id')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Appointment updated!')
            ->seeInDatabase('available_appointments', [
                'id'         => $appointment->id,
                'fixture_id' => $appointment->fixture_id,
                'role_id'    => $newRole->id,
            ]);
        $appointment->role_id = $newRole->id;

        // Change the fixture of the existing appointment
        $this->seeInDatabase('available_appointments', [
            'id'         => $appointment->id,
            'fixture_id' => $appointment->fixture_id,
            'role_id'    => $appointment->role_id,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$appointment->id]))
            ->select($newFixture->id, 'fixture_id')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Appointment updated!')
            ->seeInDatabase('available_appointments', [
                'id'         => $appointment->id,
                'fixture_id' => $newFixture->id,
                'role_id'    => $appointment->role_id,
            ]);
        $appointment->fixture_id = $newFixture->id;

        // Already existing availableAppointment in the same fixture
        /** @var AvailableAppointment $newAppointment */
        $newAppointment = factory(AvailableAppointment::class)->create(['fixture_id' => $appointment->fixture_id]);

        $this->seeInDatabase('available_appointments', [
            'id'         => $appointment->id,
            'fixture_id' => $appointment->fixture_id,
            'role_id'    => $appointment->role_id,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$appointment->id]))
            ->select($newAppointment->role_id, 'role_id')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$appointment->id]))
            ->seeInElement('.alert.alert-danger', 'Appointment already added.')
            ->seeInDatabase('available_appointments', [
                'id'         => $appointment->id,
                'fixture_id' => $appointment->fixture_id,
                'role_id'    => $appointment->role_id,
            ]);

        // Already existing availableAppointment in the same role
        /** @var AvailableAppointment $anotherAppointment */
        $anotherAppointment = factory(AvailableAppointment::class)->create(['role_id' => $appointment->role_id]);

        $this->seeInDatabase('available_appointments', [
            'id'         => $appointment->id,
            'fixture_id' => $appointment->fixture_id,
            'role_id'    => $appointment->role_id,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$appointment->id]))
            ->select($anotherAppointment->fixture_id, 'fixture_id')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$appointment->id]))
            ->seeInElement('.alert.alert-danger', 'Appointment already added.')
            ->seeInDatabase('available_appointments', [
                'id'         => $appointment->id,
                'fixture_id' => $appointment->fixture_id,
                'role_id'    => $appointment->role_id,
            ]);
    }

    public function testShowAvailableAppointment()
    {
        $this->be($this->getFakeUser());

        /** @var AvailableAppointment $appointment */
        $appointment = factory(AvailableAppointment::class)->create();

        $this->visit(route(self::BASE_ROUTE . '.show', [$appointment->id]))
            ->seeInElement('tbody tr td:nth-child(1)', $appointment->id)
            ->seeInElement('tbody tr td:nth-child(2)', $appointment->fixture)
            ->seeInElement('tbody tr td:nth-child(3)', $appointment->role);
    }

    public function testDeleteAvailableAppointment()
    {
        $this->be($this->getFakeUser());

        /** @var AvailableAppointment $appointment */
        $appointment = factory(AvailableAppointment::class)->create();
        $appointmentId = $appointment->id;

        $this->seeInDatabase('available_appointments', [
            'id'         => $appointment->id,
            'fixture_id' => $appointment->fixture_id,
            'role_id'    => $appointment->role_id,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$appointment->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->dontSeeInDatabase('available_appointments', ['id' => $appointmentId]);
    }
}
