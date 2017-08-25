<?php

namespace Tests;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Console\Kernel;
use LVA\Models\UploadJobStatus;
use LVA\User;

/**
 * Class TestCase
 *
 * Some notes about the testing DB
 *
 * I cannot use a SQLite DB as the migrations that added new columns to an existing table don't specify a default
 * and this is not acceptable for a non nullable column in SQLite. So, I need to use a MySQL DB
 *
 * Using the DatabaseMigrations trait is not an option as practically dropping and recreating the DB for every test
 * it's too time consuming
 *
 * For some reason the DatabaseTransactions trait doesn't work properly. Records are left in the DB and therefore
 * some tests will fail because the factory cannot add a new, unique, role, or season, or something else. Therefore
 * I'm left to manually start a transaction in the setUp() and roll it back in tearDown(). This seems to work quite
 * well. And quickly.
 *
 * @package Tests
 */
class TestCase extends \Laravel\BrowserKitTesting\TestCase
{
    protected static $refreshDatabase = true;
    /** @var  Generator */
    protected $faker;
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://lva.dev';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp()
    {
        parent::setUp();

        $this->faker = Factory::create(config('app.faker_locale'));

        // Refresh the DB, but only once
        if (self::$refreshDatabase) {
            \Artisan::call('migrate:refresh');
            self::$refreshDatabase = false;
        }

        // Manually start a transaction (see comment in the class PHPDoc
        \DB::beginTransaction();
    }

    protected function tearDown()
    {
        // Manually rollback the transaction
        \DB::rollback();

        parent::tearDown();
    }

    /**
     * @param array $overrides
     *
     * @return UploadJobStatus
     */
    protected function uploadJobTestFactory($overrides = [])
    {
        $totalLines = $this->faker->numberBetween(10, 200);
        $totalRows = $this->faker->numberBetween($totalLines, $totalLines * 2);

        /** @var \LVA\Models\Venue[] $mapping */
        $mapping = factory(\LVA\Models\Venue::class)->times(3)->make();

        $defaults = [
            "status_code"     => \LVA\Models\UploadJobStatus::STATUS_NOT_STARTED,
            "total_lines"     => $totalLines,
            "processed_lines" => $this->faker->numberBetween(0, $totalLines),
            "total_rows"      => $totalRows,
            "processed_rows"  => $this->faker->numberBetween(0, $totalRows),
            'processing_line' => [
                'division'     => factory(\LVA\Models\Division::class)->make()->getName(),
                'match_number' => $this->faker->numberBetween(1, 20),
                'home_team'    => factory(\LVA\Models\Team::class)->make()->getName(),
                'away_team'    => factory(\LVA\Models\Team::class)->make()->getName(),
                'date'         => $this->faker->date('D, d/m/Y'),
                'warm_up_time' => $this->faker->date('H:i'),
                'start_time'   => $this->faker->date('H:i'),
                'venue'        => factory(\LVA\Models\Venue::class)->make()->getName(),
            ],
            'unknowns'        => [
                \LVA\Models\UploadJobStatus::UNKNOWN_VENUE => [
                    ['value' => $mapping[0]->getId(), 'text' => $mapping[0]->getName()],
                    ['value' => $mapping[1]->getId(), 'text' => $mapping[1]->getName()],
                    ['value' => $mapping[2]->getId(), 'text' => $mapping[2]->getName()],
                ],
            ],
            "errors"          => $this->faker->unique()->sentences(),
            "error_line"      => $this->faker->numberBetween(1, $totalLines - 1),
        ];

        return UploadJobStatus::factory(array_merge($defaults, $overrides));
    }

    /**
     * @param string $routeName
     * @param string $breadcrumb
     */
    protected function breadcrumbsTests($routeName, $breadcrumb)
    {
        $page = $this->visit(route($routeName));

        $this->assertEquals(1, $page->crawler->filter('#breadcrumbs')->count());

        $page->seeInElement('ol.breadcrumb li.active', $breadcrumb);
    }
}
