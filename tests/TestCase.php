<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
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
class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    protected $users = [];

    protected static $refreshDatabase = true;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp()
    {
        parent::setUp();

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
     * @param int|null $userId
     *
     * @return mixed
     */
    protected function getFakeUser($userId = null)
    {
        if (is_null($userId)) {
            $password = str_random(10);
            $user = factory(User::class)
                ->create([
                    'password' => bcrypt($password),
                ]);
            $user->clearPassword = $password;
            $userId = $user->id;
            $this->users[$userId] = $user;
        } elseif (!isset($this->users[$userId])) {
            $password = str_random(10);
            $user = factory(User::class)
                ->create([
                    'id'       => $userId,
                    'password' => bcrypt($password),
                ]);
            $user->clearPassword = $password;
            $this->users[$userId] = $user;
        }

        return $this->users[$userId];
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
