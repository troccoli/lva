<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Artisan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use LVA\User;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use DatabaseTransactions;

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
        if (self::$refreshDatabase) {
            Artisan::call('migrate:refresh');
            self::$refreshDatabase = false;
        }
    }

    /**
     * @param int|null $userId
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
