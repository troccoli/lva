<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use LVA\User;
use Tests\TestCase;

class DataManagement extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function logged_in_user_can_manage_data()
    {
        $this->be(factory(User::class)->create());

        $this->get(route('admin::dataManagement'))
            ->assertSuccessful();
    }

    /**
     * @test
     */
    public function not_logged_in_user_cannot_manage_data()
    {
        $this->get(route('admin::dataManagement'))
            ->assertRedirect(route('login'));
    }
}
