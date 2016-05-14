<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:47
 */

namespace Admin\DataManagement;

use Tests\TestCase;
use App\Models\Venue;

class VenuesTableTest extends TestCase
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
        $this->be($this->getFakeUser());
        $this->breadcrumbsTests(self::BASE_ROUTE . '.index', 'Venues');
    }

    public function testAddVenue()
    {
        $this->be($this->getFakeUser());

        /** @var Venue $venue */
        $venue = factory(Venue::class)->make();

        // Brand new venue
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($venue->venue, 'venue')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Venue added!')
            ->seeInDatabase('venues', [
                'id'    => 1,
                'venue' => $venue->venue,
            ]);

        // Already existing venue
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($venue->venue, 'venue')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The venue already exists.')
            ->seeInDatabase('venues', [
                'id'    => 1,
                'venue' => $venue->venue,
            ]);
    }

    public function testEditVenue()
    {
        $this->be($this->getFakeUser());

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        /** @var Venue $newVenue */
        $newVenue = factory(Venue::class)->make();

        $this->seeInDatabase('venues', [
            'id'    => $venue->id,
            'venue' => $venue->venue,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$venue->id]))
            ->type($newVenue->venue, 'venue')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Venue updated!')
            ->seeInDatabase('venues', [
                'id'    => $venue->id,
                'venue' => $newVenue->venue,
            ]);
        $venue->venue = $newVenue->venue;
        unset($newVenue);

        /** @var Venue $newVenue */
        $newVenue = factory(Venue::class)->create();

        // Already existing venue
        $this->seeInDatabase('venues', [
            'id'    => $venue->id,
            'venue' => $venue->venue,
        ])
            ->visit(route(self::BASE_ROUTE . '.edit', [$venue->id]))
            ->type($newVenue->venue, 'venue')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$venue->id]))
            ->seeInElement('.alert.alert-danger', 'The venue already exists.')
            ->seeInDatabase('venues', [
                'id'    => $venue->id,
                'venue' => $venue->venue,
            ]);
    }

    public function testShowVenue()
    {
        $this->be($this->getFakeUser());

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        $this->visit(route(self::BASE_ROUTE . '.show', [$venue->id]))
            ->seeInElement('tbody tr td:nth-child(1)', $venue->id)
            ->seeInElement('tbody tr td:nth-child(2)', $venue->venue);
    }

    public function testDeleteVenue()
    {
        $this->be($this->getFakeUser());

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $venueId = $venue->id;

        $this->seeInDatabase('venues', [
            'id'    => $venue->id,
            'venue' => $venue->venue,
        ])
            ->call('DELETE', route(self::BASE_ROUTE . '.destroy', [$venue->id]))
            ->isRedirect(route(self::BASE_ROUTE . '.index'));

        $this->dontSeeInDatabase('venues', ['id' => $venueId]);
    }
}
