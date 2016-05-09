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

        /** @var AvailableAppointment $availableAppointment */
        $availableAppointment = factory(AvailableAppointment::class)->create();
        $availableAppointmentId = $availableAppointment->id;
        $fixtureId = $availableAppointment->fixture_id;
        $roleId = $availableAppointment->role_id;

        $otherFixtureId = factory(Fixture::class)->create()->id;
        $otherRoleId = factory(Role::class)->create()->id;

        // Brand new appointment
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($otherFixtureId, 'fixture_id')
            ->select($otherRoleId, 'role_id')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Appointment added!')
            ->seeInDatabase('available_appointments', [
                'id'         => $availableAppointmentId + 1,
                'fixture_id' => $otherFixtureId,
                'role_id'    => $otherRoleId,
            ]);

        // Already existing appointment in the same fixture with different role
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($fixtureId, 'fixture_id')
            ->select($otherRoleId, 'role_id')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Appointment added!')
            ->seeInDatabase('available_appointments', [
                'id'         => $availableAppointmentId + 2,
                'fixture_id' => $fixtureId,
                'role_id'    => $otherRoleId,
            ]);

        // Already existing appointment in the same role with different fixture
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($otherFixtureId, 'fixture_id')
            ->select($roleId, 'role_id')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Appointment added!')
            ->seeInDatabase('available_appointments', [
                'id'         => $availableAppointmentId + 3,
                'fixture_id' => $otherFixtureId,
                'role_id'    => $roleId,
            ]);

        // Already existing appointment
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->select($fixtureId, 'fixture_id')
            ->select($roleId, 'role_id')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'Appointment already added.')
            ->seeInDatabase('available_appointments', [
                'id'         => $availableAppointmentId,
                'fixture_id' => $fixtureId,
                'role_id'    => $roleId,
            ]);
    }

    public function testEditAvailableAppointment()
    {
        $this->be($this->getFakeUser());

        /** @var AvailableAppointment $availableAppointment */
        $availableAppointment = factory(AvailableAppointment::class)->create();
        $availableAppointmentId = $availableAppointment->id;
        $fixtureId = $availableAppointment->fixture_id;
        $roleId = $availableAppointment->role_id;

        $otherFixtureId = factory(Fixture::class)->create()->id;
        $otherRoleId = factory(Role::class)->create()->id;

        // Change the role of the existing appointment
        $this->seeInDatabase('available_appointments', [
            'id'         => $availableAppointmentId,
            'fixture_id' => $fixtureId,
            'role_id'    => $roleId,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$availableAppointmentId]))
            ->select($otherRoleId, 'role_id')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Appointment updated!')
            ->seeInDatabase('available_appointments', [
                'id'         => $availableAppointmentId,
                'fixture_id' => $fixtureId,
                'role_id'    => $otherRoleId,
            ]);
        $roleId = $otherRoleId;

        // Change the fixture of the existing appointment
        $this->seeInDatabase('available_appointments', [
            'id'         => $availableAppointmentId,
            'fixture_id' => $fixtureId,
            'role_id'    => $roleId,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$availableAppointmentId]))
            ->select($otherFixtureId, 'fixture_id')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Appointment updated!')
            ->seeInDatabase('available_appointments', [
                'id'         => $availableAppointmentId,
                'fixture_id' => $otherFixtureId,
                'role_id'    => $roleId,
            ]);
        $fixtureId = $otherFixtureId;

        // Already existing availableAppointment in the same fixture
        /** @var AvailableAppointment $anotherAvailableAppointment */
        $anotherAvailableAppointment = factory(AvailableAppointment::class)->create(['fixture_id' => $fixtureId]);
        $anotherAvailableAppointmentRoleId = $anotherAvailableAppointment->role_id;

        $this->seeInDatabase('available_appointments', [
            'id'         => $availableAppointmentId,
            'fixture_id' => $fixtureId,
            'role_id'    => $roleId,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$availableAppointmentId]))
            ->select($anotherAvailableAppointmentRoleId, 'role_id')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$availableAppointmentId]))
            ->seeInElement('.alert.alert-danger', 'Appointment already added.')
            ->seeInDatabase('available_appointments', [
                'id'         => $availableAppointmentId,
                'fixture_id' => $fixtureId,
                'role_id'    => $roleId,
            ]);

        // Already existing availableAppointment in the same role
        /** @var AvailableAppointment $yetAnotherAvailableAppointment */
        $yetAnotherAvailableAppointment = factory(AvailableAppointment::class)->create(['role_id' => $roleId]);
        $yetAnotherAvailableAppointmentFixtureId = $yetAnotherAvailableAppointment->fixture_id;

        $this->seeInDatabase('available_appointments', [
            'id'         => $availableAppointmentId,
            'fixture_id' => $fixtureId,
            'role_id'    => $roleId,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$availableAppointmentId]))
            ->select($yetAnotherAvailableAppointmentFixtureId, 'fixture_id')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$availableAppointmentId]))
            ->seeInElement('.alert.alert-danger', 'Appointment already added.')
            ->seeInDatabase('available_appointments', [
                'id'         => $availableAppointmentId,
                'fixture_id' => $fixtureId,
                'role_id'    => $roleId,
            ]);
    }

    public function testShowAvailableAppointment()
    {
        $this->be($this->getFakeUser());

        /** @var AvailableAppointment $availableAppointment */
        $availableAppointment = factory(AvailableAppointment::class)->create();
        $availableAppointmentId = $availableAppointment->id;
        $fixtureId = $availableAppointment->fixture_id;
        $roleId = $availableAppointment->role_id;

        /** @var Fixture $fixture */
        $fixture = Fixture::find($fixtureId);
        $fixtureName = $fixture->fixture;

        /** @var Role $role */
        $role = Role::find($roleId);
        $roleName = $role->role;

        $this->visit(route(self::BASE_ROUTE . '.show', [$availableAppointmentId]))
            ->seeInElement('tbody tr td:nth-child(1)', $availableAppointmentId)
            ->seeInElement('tbody tr td:nth-child(2)', $fixtureName)
            ->seeInElement('tbody tr td:nth-child(3)', $roleName);
    }

    public function testDeleteAvailableAppointment()
    {
        $this->be($this->getFakeUser());

        /** @var AvailableAppointment $availableAppointment */
        $availableAppointment = factory(AvailableAppointment::class)->create();
        $availableAppointmentId = $availableAppointment->id;
        $fixtureId = $availableAppointment->fixture_id;
        $roleId = $availableAppointment->role_id;

        $this->seeInDatabase('available_appointments', [
            'id'         => $availableAppointmentId,
            'fixture_id' => $fixtureId,
            'role_id'    => $roleId])
            ->call('DELETE', route(self::BASE_ROUTE . '.destroy', [$availableAppointmentId]))
            ->isRedirect(route(self::BASE_ROUTE . '.index'));

        $this->dontSeeInDatabase('available_appointments', ['id' => $availableAppointmentId]);
    }
}
