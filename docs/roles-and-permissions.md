## Roles and permissions

A big part of this project is about roles, and indirectly permissions. Everything that a user can do
is managed with roles. Yes, there are permissions for single actions, but they are assigned to roles
and a user will be given permissions only via roles.

To manage roles and permissions I use the awesome [Laravel Permission](https://github.com/spatie/laravel-permission) package by Spatie.

There will always be a `Site Administrator` role which will be able to do absolutely anything in
the site. This is achieved by implicitly grant it all permissions using the laravel `Gate` facade in the
`AuthServiceProvider`, as suggested in the package documentation.

Each season, competition, division, club and team will have a role associated with it, call administrator,
or secretary for clubs and teams. These roles will be created on the fly: when a model has been created an event
is fired, and the listener will create the correct role and permissions, and give that role the
necessary permissions.

Accessing the resource is then authorized by the use of Policies. Each policy decided which roles are allowed
to perform that operation on that model.

Let's look at an example.

When a competition is created a `\App\Events\CompetitionCreated` even is fired:

```php
// app/Models/Competition.php

protected $dispatchesEvents = [
    'created' => \App\Events\CompetitionCreated::class,
];
```

A listener has been registered to react to that event:

```php
// app/Providers/EventServiceProvider.php

protected $listen = [
    // ...
    \App\Events\CompetitionCreated::class => [
        \App\Listeners\SetUpCompetitionAdmin::class,
    ],
    // ...
];
```

This listener will dispatch jobs to create the roles. Note that these jobs are synchronous as we do
want them to proceed one after the other, and the rest of the code to continue only after the roles
have been created:

```php
\App\Jobs\CreateCompetitionAdminRole::dispatchNow($competition);
\App\Jobs\CreateCompetitionPermissions::dispatchNow($competition);
```

This is another example of doing something in a particular way just to show what it is possible. There is probably
no real reason to have jobs for these tasks just for them to be called synchronously (although there may
be cases where that is justified).

Finally, the listener will create the necessary permissions and give them to the relevant roles.
