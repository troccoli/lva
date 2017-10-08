<?php

namespace Tests\Browser\Admin\DataManagement;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use LVA\Models\AvailableAppointment;
use LVA\Models\Fixture;
use LVA\Models\Role;
use LVA\User;
use Tests\Browser\Pages\Resources\AvailableAppointmentsPage;
use Tests\DuskTestCase;

class AvailableAppointmentResourceTest extends DuskTestCase
{
    public function testRedirectIfNotAdmin()
    {
        $page = new AvailableAppointmentsPage();

        $this->browse(function (Browser $browser) use ($page) {
            $availableAppointment = factory(AvailableAppointment::class)->create();

            $browser->visit($page->indexUrl())
                ->assertRouteIs('login');

            $browser->visit($page->createUrl())
                ->assertRouteIs('login');

            $browser->visit($page->showUrl($availableAppointment->id))
                ->assertRouteIs('login');

            $browser->visit($page->editUrl($availableAppointment->id))
                ->assertRouteIs('login');

        });
    }

    public function testListAvailableAppointments()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Collection $availableAppointments */
            $availableAppointments = factory(AvailableAppointment::class)->times(20)->create();

            $page1 = $availableAppointments->slice(0, 15);
            $page2 = $availableAppointments->slice(15, 5);

            $page = new AvailableAppointmentsPage();
            $browser->visit($page)
                // Make sure we see the breadcrumb
                ->assertSeeIn($page->breadcrumb, 'Available appointments')
                // Check page 1
                ->with('tbody', function ($table) use ($page1) {
                    $child = 1;
                    foreach ($page1 as $availableAppointment) {
                        $table->with("tr:nth-child($child)", function ($row) use ($availableAppointment) {
                            $linkText = (string) $availableAppointment->fixture;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $linkText)
                                ->assertSeeIn('td:nth-child(2)', (string) $availableAppointment->role);
                        });
                        $child++;
                    }
                })
                // Check page 2
                ->with($page->pageNavigation, function ($nav) {
                    $nav->clickLink(2);
                })
                ->assertPathIs($page->indexUrl())
                ->with('tbody', function ($table) use ($page2) {
                    $child = 1;
                    foreach ($page2 as $availableAppointment) {
                        $table->with("tr:nth-child($child)", function ($row) use ($availableAppointment) {
                            $linkText = (string) $availableAppointment->fixture;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $linkText)
                                ->assertSeeIn('td:nth-child(2)', (string) $availableAppointment->role);
                        });
                        $child++;
                    }
                });
        });
    }

    public function testAddAvailableAppointment()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            $page = new AvailableAppointmentsPage();

            // Check we can add a venue from the landing page
            $browser->visit($page)
                ->clickLink('New appointment')
                ->assertPathIs($page->createUrl());

            /** @var AvailableAppointment $availableAppointment */
            $availableAppointment = factory(AvailableAppointment::class)->make();

            // Brand new appointment
            $browser->visit($page->createUrl())
                ->select('fixture_id', $availableAppointment->fixture_id)
                ->select('role_id', $availableAppointment->role_id)
                ->pressSubmit('Add')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Appointment added!');

            // Already existing appointment in the same fixture with different role
            $newRole = factory(Role::class)->create();
            $browser->visit($page->createUrl())
                ->select('fixture_id', $availableAppointment->fixture_id)
                ->select('role_id', $newRole->id)
                ->pressSubmit('Add')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Appointment added!');

            // Already existing appointment in the same role with different fixture
            $newFixture = factory(Fixture::class)->create();
            $browser->visit($page->createUrl())
                ->select('fixture_id', $newFixture->id)
                ->select('role_id', $availableAppointment->role_id)
                ->pressSubmit('Add')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Appointment added!');

            // Already existing appointment
            $browser->visit($page->createUrl())
                ->select('fixture_id', $availableAppointment->fixture_id)
                ->select('role_id', $availableAppointment->role_id)
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@form-errors', 'Appointment already added.');
        });
    }

    public function testEditAvailableAppointment()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var AvailableAppointment $availableAppointment */
            $availableAppointment = factory(AvailableAppointment::class)->create();

            $page = new AvailableAppointmentsPage();

            // Check we can edit a venue from the landing page
            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs($page->editUrl($availableAppointment->id));

            // Don't change anything
            $browser->visit($page->editUrl($availableAppointment->id))
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Appointment updated!');

            // Change the role of the existing appointment
            $newRole = factory(Role::class)->create();
            $browser->visit($page->editUrl($availableAppointment->id))
                ->select('role_id', $newRole->id)
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Appointment updated!');
            $availableAppointment->role_id = $newRole->id;

            // Change the fixture of the existing appointment
            $newFixture = factory(Fixture::class)->create();
            $browser->visit($page->editUrl($availableAppointment->id))
                ->select('fixture_id', $newFixture->id)
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Appointment updated!');
            $availableAppointment->fixture_id = $newFixture->id;

            // Already existing appointment in the same fixture
            /** @var AvailableAppointment $newAppointment */
            $newAppointment = factory(AvailableAppointment::class)->create(['fixture_id' => $availableAppointment->fixture_id]);
            $browser->visit($page->editUrl($availableAppointment->id))
                ->select('role_id', $newAppointment->role_id)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($availableAppointment->id))
                ->assertSeeIn('@form-errors', 'Appointment already added.');

            // Already existing appointment in the same role
            /** @var AvailableAppointment $anotherAppointment */
            $anotherAppointment = factory(AvailableAppointment::class)->create(['role_id' => $availableAppointment->role_id]);
            $browser->visit($page->editUrl($availableAppointment->id))
                ->select('fixture_id', $anotherAppointment->fixture_id)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($availableAppointment->id))
                ->assertSeeIn('@form-errors', 'Appointment already added.');
        });
    }

    public function testShowAvailableAppointment()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var AvailableAppointment $availableAppointment */
            $availableAppointment = factory(AvailableAppointment::class)->create();
            $linkText = $availableAppointment->venue;

            $page = new AvailableAppointmentsPage();

            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) use ($linkText) {
                    $table->clickLink($linkText);
                })
                ->assertPathIs($page->showUrl($availableAppointment->id))
                ->assertSeeIn('tbody tr td:nth-child(1)', $availableAppointment->id)
                ->assertSeeIn('tbody tr td:nth-child(2)', (string) $availableAppointment->fixture)
                ->assertSeeIn('tbody tr td:nth-child(3)', (string) $availableAppointment->role);
        });
    }

    public function testDeleteAvailableAppointment()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var AvailableAppointment $availableAppointment */
            $availableAppointment = factory(AvailableAppointment::class)->create();

            $page = new AvailableAppointmentsPage();

            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('No');
                })
                ->assertDontSee('Appointment deleted!')
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Appointment deleted!');
        });
    }
}
