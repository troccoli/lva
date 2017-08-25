<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use LVA\Models\AvailableAppointment;
use LVA\User;
use Tests\TestCase;
use LVA\Models\Role;

class RoleResourceTest extends TestCase
{
    const BASE_ROUTE = 'roles';

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
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Roles');
    }

    public function testAddRole()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Role $role */
        $role = factory(Role::class)->make();

        // Brand new role
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($role->role, 'role')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Role added!')
            ->seeInDatabase('roles', [
                'role' => $role->role,
            ]);

        // Already existing role
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($role->role, 'role')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The role already exists.')
            ->seeInDatabase('roles', [
                'role' => $role->role,
            ]);
    }

    public function testEditRole()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Role $role */
        $role = factory(Role::class)->create();

        // Don't change anything
        $this->seeInDatabase('roles', [
            'id'   => $role->id,
            'role' => $role->role,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$role->id]))
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Role updated!')
            ->seeInDatabase('roles', [
                'id'   => $role->id,
                'role' => $role->role,
            ]);

        /** @var Role $newRole */
        $newRole = factory(Role::class)->make();

        $this->seeInDatabase('roles', [
            'id'   => $role->id,
            'role' => $role->role,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$role->id]))
            ->type($newRole->role, 'role')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Role updated!')
            ->seeInDatabase('roles', [
                'id'   => $role->id,
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
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Role $role */
        $role = factory(Role::class)->create();

        $this->visit(route(self::BASE_ROUTE . '.show', [$role->id]))
            ->seeInElement('tbody tr td:nth-child(1)', $role->id)
            ->seeInElement('tbody tr td:nth-child(2)', $role->role);
    }

    public function testDeleteRole()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

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
