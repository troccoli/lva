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
        $venue = factory(Venue::class)->create();
        $venueId = $venue->id;
        $venueName = $venue->venue;

        // Brand new venue
        $newVenueName = 'New ' . $venueName;
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($newVenueName, 'venue')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Venue added!')
            ->seeInDatabase('venues', ['id' => $venueId + 1, 'venue' => $newVenueName]);

        // Already existing venue
        $this->visit(route(self::BASE_ROUTE . '.create'))
            ->type($venueName, 'venue')
            ->press('Add')
            ->seePageIs(route(self::BASE_ROUTE . '.create'))
            ->seeInElement('.alert.alert-danger', 'The venue already exists.')
            ->seeInDatabase('venues', ['id' => $venueId, 'venue' => $venueName]);
    }

    public function testEditVenue()
    {
        $this->be($this->getFakeUser());

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $venueId = $venue->id;
        $venueName = $venue->venue;

        $newVenueName = 'New ' . $venueName;
        $this->seeInDatabase('venues', ['id' => $venueId, 'venue' => $venueName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$venueId]))
            ->type($newVenueName, 'venue')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.index'))
            ->seeInElement('#flash-notification .alert.alert-success', 'Venue updated!')
            ->seeInDatabase('venues', ['id' => $venueId, 'venue' => $newVenueName]);
        $venueName = $newVenueName;

        $anotherVenue = factory(Venue::class)->create();
        $anotherVenueName = $anotherVenue->venue;

        // Already existing venue
        $this->seeInDatabase('venues', ['id' => $venueId, 'venue' => $venueName])
            ->visit(route(self::BASE_ROUTE . '.edit', [$venueId]))
            ->type($anotherVenueName, 'venue')
            ->press('Update')
            ->seePageIs(route(self::BASE_ROUTE . '.edit', [$venueId]))
            ->seeInElement('.alert.alert-danger', 'The venue already exists.')
            ->seeInDatabase('venues', ['id' => $venueId, 'venue' => $venueName]);
    }

    public function testShowVenue()
    {
        $this->be($this->getFakeUser());

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $venueId = $venue->id;
        $venueName = $venue->venue;

        $this->visit(route(self::BASE_ROUTE . '.show', [$venueId]))
            ->seeInElement('tbody tr td:nth-child(1)', $venueId)
            ->seeInElement('tbody tr td:nth-child(2)', $venueName);
    }

    public function testDeleteVenue()
    {
        $this->be($this->getFakeUser());

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $venueId = $venue->id;
        $venueName = $venue->venue;

        $this->seeInDatabase('venues', ['id' => $venueId, 'venue' => $venueName])
            ->call('DELETE', route(self::BASE_ROUTE . '.destroy', [$venueId]))
            ->isRedirect(route(self::BASE_ROUTE . '.index'));
        $this->dontSeeInDatabase('venues', ['id' => $venueId]);
    }
}
