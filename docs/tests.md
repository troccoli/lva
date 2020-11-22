## Test Strategy

I normally try and write Unit and either Integration or Feature tests. But at times this doesn't
make much sense, for example for the `AccessibleFixtures` repository.

The `\App\Http\Controllers\Api\V1\FixturesController` is basically a proxy for `AccessibleFixtures`
and therefore the extensive integration tests written for the `/api/v1/fixtures` endpoint
are quite enough and act as a de-facto integration test for the repository. This is why there is no
integration test for it.

This is not the case for other repositories, for example `AccessibleSeasons`, `Accessible Competitions`
and `AccessibleDivisions`. This was on purpose and done for completeness.

## Test data with Factories and Builders

A very common practice for creating test data is to use the excellent `fzaninotto/faker` package,
and the `factory()` method it provides. I believe it comes as default with Laravel.

I have been using it for years in every Laravel (or Lumen) project I worked on and this project
is no exception. However, here I have also used a different technique, a sort of wrapper around
the factories, to make it more fluent and readable: Builders.

Compare the following way to create a club and a team using factories:

```php
$club = factory(\App\Models\Club::class)->create([
  'name' => 'Arsenal Club',
]);

$team = factory(\App\Models\Team::clas)->create([
  'name'    => 'The Gunners Man',
  'club_id' => $club->id,
]);
```

and using builders:

```php
$club = aClub()->named('Arsenal Club')->build();

$team = aTeam()->named('The Gunners Man')->inClub($club)->build();
```

Much more readable and easier to understand. It also hides the details of how the model
is structured. For example, if the name of a club is not stored in the `name` field
any more, we will need only to change the builder, rather than all the occurences where
the factory has been used.

The downside of using these builders is that I have to declare global functions to access
them: see [helpers.php](../tests/Builders/helpers.php).

To avoid this I took another approach: a [Test Model Factory](../tests/Builders/TestModelFactory.php).
This is a final class that provides static methods to access the builder, so I can now write
code like

```php
$role = \Tests\Builders\TestModelFactory::aRole()->named('Marketing Officer')->build();
```
