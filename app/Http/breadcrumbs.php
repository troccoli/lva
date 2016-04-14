<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 17:16
 */

// Home
Breadcrumbs::register('home', function($b) {
    $b->push('Home', route('home'));
});

// Auth
Breadcrumbs::register('login', function($b) {
    $b->parent('home');
    $b->push('Login', route('login'));
});

Breadcrumbs::register('register', function($b) {
    $b->parent('home');
    $b->push('Register', route('register'));
});

Breadcrumbs::register('passwordReset', function($b) {
    $b->parent('home');
    $b->push('Reset Password', route('passwordReset'));
});

// Data Management
Breadcrumbs::register('admin::dataManagement', function($b) {
    $b->parent('home');
    $b->push('Data Management', route('admin::dataManagement'));
});

// Data Management - Seasons
Breadcrumbs::register('admin.data-management.seasons.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Seasons', route('admin.data-management.seasons.index'));
});
Breadcrumbs::register('admin.data-management.seasons.create', function($b) {
    $b->parent('admin.data-management.seasons.index');
    $b->push('Add', route('admin.data-management.seasons.create'));
});
Breadcrumbs::register('admin.data-management.seasons.edit', function($b) {
    $b->parent('admin.data-management.seasons.index');
    $b->push('Edit');
});
Breadcrumbs::register('admin.data-management.seasons.show', function($b) {
    $b->parent('admin.data-management.seasons.index');
    $b->push('View');
});

// Data Management - Clubs
Breadcrumbs::register('admin.data-management.clubs.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Clubs', route('admin.data-management.clubs.index'));
});
Breadcrumbs::register('admin.data-management.clubs.create', function($b) {
    $b->parent('admin.data-management.clubs.index');
    $b->push('Add', route('admin.data-management.clubs.create'));
});
Breadcrumbs::register('admin.data-management.clubs.edit', function($b) {
    $b->parent('admin.data-management.clubs.index');
    $b->push('Edit');
});
Breadcrumbs::register('admin.data-management.clubs.show', function($b) {
    $b->parent('admin.data-management.clubs.index');
    $b->push('View');
});

// Data Management - Venues
Breadcrumbs::register('admin.data-management.venues.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Venues', route('admin.data-management.venues.index'));
});
Breadcrumbs::register('admin.data-management.venues.create', function($b) {
    $b->parent('admin.data-management.venues.index');
    $b->push('Add', route('admin.data-management.venues.create'));
});
Breadcrumbs::register('admin.data-management.venues.edit', function($b) {
    $b->parent('admin.data-management.venues.index');
    $b->push('Edit');
});
Breadcrumbs::register('admin.data-management.venues.show', function($b) {
    $b->parent('admin.data-management.venues.index');
    $b->push('View');
});

// Data Management - Roles
Breadcrumbs::register('admin.data-management.roles.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Roles', route('admin.data-management.roles.index'));
});
Breadcrumbs::register('admin.data-management.roles.create', function($b) {
    $b->parent('admin.data-management.roles.index');
    $b->push('Add', route('admin.data-management.roles.create'));
});
Breadcrumbs::register('admin.data-management.roles.edit', function($b) {
    $b->parent('admin.data-management.roles.index');
    $b->push('Edit');
});
Breadcrumbs::register('admin.data-management.roles.show', function($b) {
    $b->parent('admin.data-management.roles.index');
    $b->push('View');
});

// Data Management - Divisions
Breadcrumbs::register('admin.data-management.divisions.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Divisions', route('admin.data-management.divisions.index'));
});
Breadcrumbs::register('admin.data-management.divisions.create', function($b) {
    $b->parent('admin.data-management.divisions.index');
    $b->push('Add', route('admin.data-management.divisions.create'));
});
Breadcrumbs::register('admin.data-management.divisions.edit', function($b) {
    $b->parent('admin.data-management.divisions.index');
    $b->push('Edit');
});
Breadcrumbs::register('admin.data-management.divisions.show', function($b) {
    $b->parent('admin.data-management.divisions.index');
    $b->push('View');
});

// Data Management - Teams
Breadcrumbs::register('admin.data-management.teams.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Teams', route('admin.data-management.teams.index'));
});
Breadcrumbs::register('admin.data-management.teams.create', function($b) {
    $b->parent('admin.data-management.teams.index');
    $b->push('Add', route('admin.data-management.teams.create'));
});
Breadcrumbs::register('admin.data-management.teams.edit', function($b) {
    $b->parent('admin.data-management.teams.index');
    $b->push('Edit');
});
Breadcrumbs::register('admin.data-management.teams.show', function($b) {
    $b->parent('admin.data-management.teams.index');
    $b->push('View');
});