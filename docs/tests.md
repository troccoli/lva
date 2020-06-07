## Factories and Builders

A very common practice for creating test data is to use the excellent `fzaninotto/faker` package,
and the `factory()` method it provides. I believe it comes as default with Laravel.

I have been using it for years in every Laravel (or Lumen) project I worked on and this project
is not exception. However, here I have also used a different technique, a sort of wrapper around
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

with how we can do the same using builders:

```php
$club = aClub()->named('Arsenal Club)->build();

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
