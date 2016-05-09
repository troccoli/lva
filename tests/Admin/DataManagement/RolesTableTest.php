<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

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
        $role = factory(Role::class)->create();
        $roleId = $role->id;
        $roleName = $role->role;

        // Brand new role
        $newRoleName = 'New ' . $roleName;
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($newRoleName, 'role')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Role added!')
            ->seeInDatabase('roles', ['id' => $roleId + 1, 'role' => $newRoleName]);

        // Already existing role
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($roleName, 'role')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The role already exists.')
            ->seeInDatabase('roles', ['id' => $roleId, 'role' => $roleName]);
    }

    public function testEditRole()
    {
        $this->be($this->getFakeUser());

        /** @var Role $role */
        $role = factory(Role::class)->create();
        $roleId = $role->id;
        $roleName = $role->role;

        $newRoleName = 'New ' . $roleName;
        $this->seeInDatabase('roles', ['id' => $roleId, 'role' => $roleName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$roleId]))
            ->type($newRoleName, 'role')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Role updated!')
            ->seeInDatabase('roles', ['id' => $roleId, 'role' => $newRoleName]);
        $roleName = $newRoleName;

        $anotherRole = factory(Role::class)->create();
        $anotherRoleName = $anotherRole->role;

        // Already existing role
        $this->seeInDatabase('roles', ['id' => $roleId, 'role' => $roleName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$roleId]))
            ->type($anotherRoleName, 'role')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$roleId]))
            ->seeInElement('.alert.alert-danger', 'The role already exists.')
            ->seeInDatabase('roles', ['id' => $roleId, 'role' => $roleName]);
    }

    public function testShowRole()
    {
        $this->be($this->getFakeUser());

        /** @var Role $role */
        $role = factory(Role::class)->create();
        $roleId = $role->id;
        $roleName = $role->role;

        $this->visit(route(self::BASE_ROUTE . '.show', [$roleId]))
            ->seeInElement('tbody tr td:nth-child(1)', $roleId)
            ->seeInElement('tbody tr td:nth-child(2)', $roleName);
    }

    public function testDeleteRole()
    {
        $this->be($this->getFakeUser());

        /** @var Role $role */
        $role = factory(Role::class)->create();
        $roleId = $role->id;
        $roleName = $role->role;

        $this->seeInDatabase('roles', ['id' => $roleId, 'role' => $roleName])
            ->call('DELETE', route(self::BASE_ROUTE . '.destroy', [$roleId]))
            ->isRedirect(route(self::BASE_ROUTE . '.index'));
        $this->dontSeeInDatabase('roles', ['id' => $roleId]);
    }
}
