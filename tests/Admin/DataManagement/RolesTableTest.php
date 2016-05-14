<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use App\Models\AvailableAppointment;
use Tests\TestCase;
use App\Models\Role;

class RolesTableTest extends TestCase
{
    const BASE_ROUTE = 'admin.data-management.roles';

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
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Roles');
    }

    public function testAddRole()
    {
        $this->be($this->getFakeUser());

        /** @var Role $role */
        $role = factory(Role::class)->make();

        // Brand new role
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($role->role, 'role')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Role added!')
            ->seeInDatabase('roles', [
                'id'   => 1,
                'role' => $role->role,
            ]);

        // Already existing role
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($role->role, 'role')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The role already exists.')
            ->seeInDatabase('roles', [
                'id'   => 1,
                'role' => $role->role,
            ]);
    }

    public function testEditRole()
    {
        $this->be($this->getFakeUser());

        /** @var Role $role */
        $role = factory(Role::class)->create();

        /** @var Role $newRole */
        $newRole = factory(Role::class)->make();


        $this->seeInDatabase('roles', [
            'id'   => 1,
            'role' => $role->role,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$role->id]))
            ->type($newRole->role, 'role')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Role updated!')
            ->seeInDatabase('roles', [
                'id'   => 1,
                'role' => $newRole->role,
            ]);
        $role->role = $newRole->role;
        unset($newRole);

        /** @var Role $newRole */
        $newRole = factory(Role::class)->create();

        // Already existing role
        $this->seeInDatabase('roles', [
            'id'   => $role->id,
            'role' => $role->role,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$role->id]))
            ->type($newRole->role, 'role')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$role->id]))
            ->seeInElement('.alert.alert-danger', 'The role already exists.')
            ->seeInDatabase('roles', [
                'id'   => $role->id,
                'role' => $role->role]);
    }

    public function testShowRole()
    {
        $this->be($this->getFakeUser());

        /** @var Role $role */
        $role = factory(Role::class)->create();

        $this->visit(route(self::BASE_ROUTE . '.show', [$role->id]))
            ->seeInElement('tbody tr td:nth-child(1)', $role->id)
            ->seeInElement('tbody tr td:nth-child(2)', $role->role);
    }

    public function testDeleteRole()
    {
        $this->be($this->getFakeUser());

        /** @var Role $role */
        $role = factory(Role::class)->create();
        $roleId = $role->id;

        $this->seeInDatabase('roles', [
            'id'   => $role->id,
            'role' => $role->role,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$role->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Role deleted!')
            ->dontSeeInDatabase('roles', ['id' => $roleId]);

        // Delete a role with available appointments
        /** @var Role $role */
        $role = factory(Role::class)->create();
        /** @var AvailableAppointment $appointment */
        $appointment = factory(AvailableAppointment::class)->create(['role_id' => $role->id]);

        $this->seeInDatabase('roles', [
            'id'   => $role->id,
            'role' => $role->role,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$role->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-danger', 'Cannot delete because they are existing appointments for this role.')
            ->seeInDatabase('roles', [
                'id'   => $role->id,
                'role' => $role->role,
            ]);
    }
}
