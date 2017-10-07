<?php

namespace Tests\Browser\Admin\DataManagement;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use LVA\Models\AvailableAppointment;
use LVA\Models\Role;
use LVA\User;
use Tests\Browser\Pages\Resources\RolesPage;
use Tests\DuskTestCase;

class RoleResourceTest extends DuskTestCase
{
    public function testRedirectIfNotAdmin()
    {
        $page = new RolesPage();

        $this->browse(function (Browser $browser) use ($page) {
            $role = factory(Role::class)->create();

            $browser->visit($page->indexUrl())
                ->assertRouteIs('login');

            $browser->visit($page->createUrl())
                ->assertRouteIs('login');

            $browser->visit($page->showUrl($role->id))
                ->assertRouteIs('login');

            $browser->visit($page->editUrl($role->id))
                ->assertRouteIs('login');

        });
    }

    public function testListRoles()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Collection $roles */
            $roles = factory(Role::class)->times(20)->create();

            $page1 = $roles->slice(0, 15);
            $page2 = $roles->slice(15, 5);

            $page = new RolesPage();
            $browser->visit($page)
                ->assertSeeIn($page->breadcrumb, 'Roles')
                ->assertSeeLink('New role')
                ->with('tbody', function ($table) use ($page1) {
                    $child = 1;
                    foreach ($page1 as $role) {
                        $table->with("tr:nth-child($child)", function ($row) use ($role) {
                            $linkText = $role->role;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $linkText);
                        });
                        $child++;
                    }
                })
                ->with($page->pageNavigation, function ($nav) {
                    $nav->clickLink(2);
                })
                ->assertPathIs($page->indexUrl())
                ->with('tbody', function ($table) use ($page2) {
                    $child = 1;
                    foreach ($page2 as $role) {
                        $table->with("tr:nth-child($child)", function ($row) use ($role) {
                            $linkText = $role->role;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $linkText);
                        });
                        $child++;
                    }
                });
        });
    }

    public function testAddRole()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            $page = new RolesPage();

            // Check we can add a role from the landing page
            $browser->visit($page)
                ->clickLink('New role')
                ->assertPathIs($page->createUrl());

            // All missing fields
            $browser->visit($page->createUrl())
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@role-error', 'The role field is required.')
                ->assertVisible('@form-errors');

            /** @var Role $role */
            $role = factory(Role::class)->make();
            // Brand new role
            $browser->visit($page->createUrl())
                ->type('role', $role->role)
                ->pressSubmit('Add')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Role added!');

            // Add the same role
            $browser->visit($page->createUrl())
                ->type('role', $role->role)
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@role-error', 'The role already exists.')
                ->assertVisible('@form-errors');
        });
    }

    public function testEditRole()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Role $role */
            $role = factory(Role::class)->create();

            $page = new RolesPage();

            // Check we can edit a role from the landing page
            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs($page->editUrl($role->id));

            // Don't change anything
            $browser->visit($page->editUrl($role->id))
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Role updated!');

            /** @var Role $newRole */
            $newRole = factory(Role::class)->make();

            // Edit all details
            $browser->visit($page->editUrl($role->id))
                ->type('role', $newRole->role)
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Role updated!');

            /** @var Role $newRole */
            $newRole = factory(Role::class)->create();

            // Use an already existing role
            $browser->visit($page->editUrl($role->id))
                ->type('role', $newRole->role)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($role->id))
                ->assertSeeIn('@role-error', 'The role already exists.')
                ->assertVisible('@form-errors');
        });
    }

    public function testShowRole()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Role $role */
            $role = factory(Role::class)->create();
            $linkText = $role->role;

            $page = new RolesPage();

            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) use ($linkText) {
                    $table->clickLink($linkText);
                })
                ->assertPathIs($page->showUrl($role->id))
                ->assertSeeIn('tbody tr td:nth-child(1)', $role->id)
                ->assertSeeIn('tbody tr td:nth-child(2)', $role->role);
        });
    }

    public function testDeleteRole()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Role $role */
            $role = factory(Role::class)->create();

            $page = new RolesPage();

            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('No');
                })
                ->assertDontSee('Role deleted!')
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Role deleted!');

            // Delete role with existing appointment
            $role = factory(Role::class)->create();
            factory(AvailableAppointment::class)->create(['role_id' => $role->id]);
            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Cannot delete because they are existing appointments for this role.');
        });
    }
}
