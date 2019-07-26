<?php

use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

Breadcrumbs::for('home', function (BreadcrumbsGenerator $trail) {
    $trail->push('Home', route('home'));
});
Breadcrumbs::for('dashboard', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Dashboard', route('dashboard'));
});
Breadcrumbs::for('login', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Login', route('login'));
});
Breadcrumbs::for('register', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Register', route('register'));
});
Breadcrumbs::for('password.request', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Forgotten password', route('password.request'));
});
Breadcrumbs::for('password.reset', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Reset password', route('password.request'));
});
Breadcrumbs::for('verification.notice', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Email verification');
});
Breadcrumbs::for('seasons.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('dashboard');
    $trail->push('Seasons', route('seasons.index'));
});
Breadcrumbs::for('seasons.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('seasons.index');
    $trail->push('New season');
});
Breadcrumbs::for('seasons.edit', function (BreadcrumbsGenerator $trail) {
    $trail->parent('seasons.index');
    $trail->push('Edit seasons');
});
Breadcrumbs::for('competitions.index', function (BreadcrumbsGenerator $trail, $season) {
    $trail->parent('seasons.index');
    $trail->push('Competitions', route('competitions.index', [$season]));
});
Breadcrumbs::for('competitions.create', function (BreadcrumbsGenerator $trail, $season) {
    $trail->parent('competitions.index', $season);
    $trail->push('New competition');
});
Breadcrumbs::for('competitions.edit', function (BreadcrumbsGenerator $trail, $season) {
    $trail->parent('competitions.index', $season);
    $trail->push('Edit competition');
});
Breadcrumbs::for('divisions.index', function (BreadcrumbsGenerator $trail, $competition) {
    $trail->parent('competitions.index', $competition->getSeason());
    $trail->push('Divisions', route('divisions.index', [$competition]));
});
Breadcrumbs::for('divisions.create', function (BreadcrumbsGenerator $trail, $competition) {
    $trail->parent('divisions.index', $competition);
    $trail->push('New division');
});
Breadcrumbs::for('divisions.edit', function (BreadcrumbsGenerator $trail, $competition) {
    $trail->parent('divisions.index', $competition);
    $trail->push('Edit division');
});
Breadcrumbs::for('clubs.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('dashboard');
    $trail->push('Clubs', route('clubs.index'));
});
Breadcrumbs::for('clubs.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('clubs.index');
    $trail->push('New club');
});
Breadcrumbs::for('clubs.edit', function (BreadcrumbsGenerator $trail) {
    $trail->parent('clubs.index');
    $trail->push('Edit club');
});
Breadcrumbs::for('teams.index', function (BreadcrumbsGenerator $trail, $club) {
    $trail->parent('clubs.index');
    $trail->push('Teams', route('teams.index', [$club]));
});
Breadcrumbs::for('teams.create', function (BreadcrumbsGenerator $trail, $club) {
    $trail->parent('teams.index', $club);
    $trail->push('New team');
});
Breadcrumbs::for('teams.edit', function (BreadcrumbsGenerator $trail, $club) {
    $trail->parent('teams.index', $club);
    $trail->push('Edit team');
});
