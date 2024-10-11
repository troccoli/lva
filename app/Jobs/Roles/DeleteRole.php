<?php

namespace App\Jobs\Roles;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DeleteRole implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Collection $roles) {}

    public function handle(): void
    {
        DB::transaction(function (): void {
            $this->roles->each(function (Role $role): void {
                $role->users->each(function (User $user) use ($role): void {
                    $user->removeRole($role);
                });
                $role->delete();
            });
        });
    }
}
