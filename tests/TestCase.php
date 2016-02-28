<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    protected $users = [];

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

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp()
    {
        parent::setUp();
        Artisan::call('migrate:refresh');
    }

    protected function getFakeUser($userId = null)
    {
        if (is_null($userId)) {
            $password = str_random(10);
            $user = factory(App\User::class)
                ->create([
                    'password' => bcrypt($password),
                ]);
            $user->clearPassword = $password;
            $userId = $user->id;
            $this->users[$userId] = $user;
        } elseif (!isset($this->users[$userId])) {
            $password = str_random(10);
            $user = factory(App\User::class)
                ->create([
                    'id'       => $userId,
                    'password' => bcrypt($password),
                ]);
            $user->clearPassword = $password;
            $this->users[$userId] = $user;
        }
    
        return $this->users[$userId];
    }
}
