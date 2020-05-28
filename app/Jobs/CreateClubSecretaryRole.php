<?php

namespace App\Jobs;

use App\Models\Club;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Permission\Models\Role;

class CreateClubSecretaryRole
{
    use Dispatchable, Queueable;

    private Club $club;

    public function __construct(Club $club)
    {
        $this->club = $club;
    }

    public function handle(): void
    {
        Role::create(['name' => $this->club->getSecretaryRole()]);
    }
}
