<?php

namespace App\Providers;

use App\Events\ClubCreated;
use App\Events\CompetitionCreated;
use App\Events\DivisionCreated;
use App\Events\SeasonCreated;
use App\Listeners\CreateClubSecretaryRole;
use App\Listeners\CreateCompetitionAdminRole;
use App\Listeners\CreateDivisionAdminRole;
use App\Listeners\CreateSeasonAdminRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SeasonCreated::class => [
            CreateSeasonAdminRole::class,
        ],
        CompetitionCreated::class => [
            CreateCompetitionAdminRole::class,
        ],
        DivisionCreated::class => [
            CreateDivisionAdminRole::class,
        ],
        ClubCreated::class => [
            CreateClubSecretaryRole::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
