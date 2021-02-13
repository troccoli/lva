<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator;

Breadcrumbs::for('home', function (Generator $trail) {
    $trail->push('Home', route('home'));
});
Breadcrumbs::for('dashboard', function (Generator $trail) {
    $trail->parent('home');
    $trail->push('Dashboard', route('dashboard'));
});
Breadcrumbs::for('login', function (Generator $trail) {
    $trail->parent('home');
    $trail->push('Login', route('login'));
});
Breadcrumbs::for('register', function (Generator $trail) {
    $trail->parent('home');
    $trail->push('Register', route('register'));
});
Breadcrumbs::for('password.request', function (Generator $trail) {
    $trail->parent('home');
    $trail->push('Forgotten password', route('password.request'));
});
Breadcrumbs::for('password.reset', function (Generator $trail) {
    $trail->parent('home');
    $trail->push('Reset password', route('password.request'));
});
Breadcrumbs::for('verification.notice', function (Generator $trail) {
    $trail->parent('home');
    $trail->push('Email verification');
});
Breadcrumbs::for('seasons.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Seasons', route('seasons.index'));
});
Breadcrumbs::for('seasons.create', function (Generator $trail) {
    $trail->parent('seasons.index');
    $trail->push('New season');
});
Breadcrumbs::for('seasons.edit', function (Generator $trail) {
    $trail->parent('seasons.index');
    $trail->push('Edit seasons');
});
Breadcrumbs::for('competitions.index', function (Generator $trail, $season) {
    $trail->parent('seasons.index');
    $trail->push('Competitions', route('competitions.index', [$season]));
});
Breadcrumbs::for('competitions.create', function (Generator $trail, $season) {
    $trail->parent('competitions.index', $season);
    $trail->push('New competition');
});
Breadcrumbs::for('competitions.edit', function (Generator $trail, $season) {
    $trail->parent('competitions.index', $season);
    $trail->push('Edit competition');
});
Breadcrumbs::for('divisions.index', function (Generator $trail, $competition) {
    $trail->parent('competitions.index', $competition->getSeason());
    $trail->push('Divisions', route('divisions.index', [$competition]));
});
Breadcrumbs::for('divisions.create', function (Generator $trail, $competition) {
    $trail->parent('divisions.index', $competition);
    $trail->push('New division');
});
Breadcrumbs::for('divisions.edit', function (Generator $trail, $competition) {
    $trail->parent('divisions.index', $competition);
    $trail->push('Edit division');
});
Breadcrumbs::for('clubs.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Clubs', route('clubs.index'));
});
Breadcrumbs::for('clubs.create', function (Generator $trail) {
    $trail->parent('clubs.index');
    $trail->push('New club');
});
Breadcrumbs::for('clubs.edit', function (Generator $trail) {
    $trail->parent('clubs.index');
    $trail->push('Edit club');
});
Breadcrumbs::for('teams.index', function (Generator $trail, $club) {
    $trail->parent('clubs.index');
    $trail->push('Teams', route('teams.index', [$club]));
});
Breadcrumbs::for('teams.create', function (Generator $trail, $club) {
    $trail->parent('teams.index', $club);
    $trail->push('New team');
});
Breadcrumbs::for('teams.edit', function (Generator $trail, $club) {
    $trail->parent('teams.index', $club);
    $trail->push('Edit team');
});
Breadcrumbs::for('venues.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Venues', route('venues.index'));
});
Breadcrumbs::for('venues.show', function (Generator $trail, $venue) {
    $trail->parent('venues.index');
    $trail->push($venue->getName());
});
Breadcrumbs::for('venues.create', function (Generator $trail) {
    $trail->parent('venues.index');
    $trail->push('New venue');
});
Breadcrumbs::for('venues.edit', function (Generator $trail, $venue) {
    $trail->parent('venues.index');
    $trail->push('Edit venue');
});
Breadcrumbs::for('fixtures.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Fixtures', route('fixtures.index'));
});
