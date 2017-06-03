<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use LVA\Models\Fixture;
use LVA\User;
use Tests\TestCase;
use LVA\Models\Venue;

class VenueResourceTest extends TestCase
{
    const BASE_ROUTE = 'admin.data-management.venues';

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
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Venues');
    }

    public function testAddVenue()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Venue $venue */
        $venue = factory(Venue::class)->make();

        // Brand new venue
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($venue->venue, 'venue')
            ->type($venue->directions, 'directions')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Venue added!')
            ->seeInDatabase('venues', [
                'venue'      => $venue->venue,
                'directions' => $venue->directions,
            ]);

        /** @var Venue $venue2 */
        $venue2 = factory(Venue::class)->make();

        // Brand new venue without directions
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($venue2->venue, 'venue')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Venue added!')
            ->seeInDatabase('venues', [
                'venue'      => $venue2->venue,
                'directions' => '',
            ]);

        // Already existing venue
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($venue->venue, 'venue')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The venue already exists.')
            ->seeInDatabase('venues', [
                'venue' => $venue->venue,
            ]);
    }

    public function testEditVenue()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        // Don't change anything
        $this->seeInDatabase('venues', [
            'id'         => $venue->id,
            'venue'      => $venue->venue,
            'directions' => $venue->directions,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$venue->id]))
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Venue updated!')
            ->seeInDatabase('venues', [
                'id'         => $venue->id,
                'venue'      => $venue->venue,
                'directions' => $venue->directions,
            ]);

        /** @var Venue $newVenue */
        $newVenue = factory(Venue::class)->make();

        $this->seeInDatabase('venues', [
            'id'         => $venue->id,
            'venue'      => $venue->venue,
            'directions' => $venue->directions,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$venue->id]))
            ->type($newVenue->venue, 'venue')
            ->type($newVenue->directions, 'directions')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Venue updated!')
            ->seeInDatabase('venues', [
                'id'         => $venue->id,
                'venue'      => $newVenue->venue,
                'directions' => $newVenue->directions,
            ]);
        $venue->venue = $newVenue->venue;
        $venue->directions = $newVenue->directions;
        unset($newVenue);

        /** @var Venue $newVenue */
        $newVenue = factory(Venue::class)->create();

        // Already existing venue
        $this->seeInDatabase('venues', [
            'id'         => $venue->id,
            'venue'      => $venue->venue,
            'directions' => $venue->directions,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$venue->id]))
            ->type($newVenue->venue, 'venue')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$venue->id]))
            ->seeInElement('.alert.alert-danger', 'The venue already exists.')
            ->seeInDatabase('venues', [
                'id'         => $venue->id,
                'venue'      => $venue->venue,
                'directions' => $venue->directions,
            ]);
    }

    public function testShowVenue()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        $this->visit(route(self::BASE_ROUTE . '.show', [$venue->id]))
            ->seeInElement('tbody tr td:nth-child(1)', $venue->id)
            ->seeInElement('tbody tr td:nth-child(2)', $venue->venue)
            ->seeInElement('tbody tr td:nth-child(3)', $venue->directions);
    }

    public function testDeleteVenue()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->be($user);

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $venueId = $venue->id;

        $this->seeInDatabase('venues', [
            'id'         => $venue->id,
            'venue'      => $venue->venue,
            'directions' => $venue->directions,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$venue->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Venue deleted!')
            ->dontSeeInDatabase('venues', ['id' => $venueId]);

        // Delete a venue with fixtures
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $fixture = factory(Fixture::class)->create(['venue_id' => $venue->id]);

        $this->seeInDatabase('venues', [
            'id'         => $venue->id,
            'venue'      => $venue->venue,
            'directions' => $venue->directions,
        ])
            ->makeRequest('DELETE', route(self::BASE_ROUTE . '.destroy', [$venue->id]))
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-danger', 'Cannot delete because they are existing fixtures at this venue.')
            ->seeInDatabase('venues', [
                'id'         => $venue->id,
                'venue'      => $venue->venue,
                'directions' => $venue->directions,
            ]);
    }
}