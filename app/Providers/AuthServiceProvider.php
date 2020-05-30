<?php

namespace App\Providers;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Season;
use App\Policies\CompetitionPolicy;
use App\Policies\DivisionPolicy;
use App\Policies\FixturePolicy;
use App\Policies\SeasonPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Season::class => SeasonPolicy::class,
        Competition::class => CompetitionPolicy::class,
        Division::class => DivisionPolicy::class,
        Fixture::class => FixturePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        // Implicitly grant "Site Administrator" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Site Administrator') ? true : null;
        });
    }
}
