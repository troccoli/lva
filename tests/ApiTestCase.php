<?php

namespace Tests;

use App\Models\User;
use Laravel\Passport\Passport;

abstract class ApiTestCase extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        Passport::actingAs(factory(User::class)->create());
    }
}
