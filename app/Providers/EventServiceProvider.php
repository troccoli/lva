<?php

namespace App\Providers;

use App\Events\ClubCreated;
use App\Events\CompetitionCreated;
use App\Events\DivisionCreated;
use App\Events\SeasonCreated;
use App\Events\TeamCreated;
use App\Listeners\SetUpClubSecretary;
use App\Listeners\SetUpCompetitionAdmin;
use App\Listeners\SetUpDivisionAdmin;
use App\Listeners\SetUpSeasonAdmin;
use App\Listeners\SetUpTeamSecretary;
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
            SetUpSeasonAdmin::class,
        ],
        CompetitionCreated::class => [
            SetUpCompetitionAdmin::class,
        ],
        DivisionCreated::class => [
            SetUpDivisionAdmin::class,
        ],
        ClubCreated::class => [
            SetUpClubSecretary::class,
        ],
        TeamCreated::class => [
            SetUpTeamSecretary::class,
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
