<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DeleteRole implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Collection $roles;

    public function __construct(Collection $roles)
    {
        $this->roles = $roles;
    }

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
