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
    $trail->parent('home');
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
