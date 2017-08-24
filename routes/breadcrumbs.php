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
    $b->push('Data management', route('admin::dataManagement'));
});

// Data Management - Seasons
Breadcrumbs::register('seasons.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Seasons', route('seasons.index'));
});
Breadcrumbs::register('seasons.create', function($b) {
    $b->parent('seasons.index');
    $b->push('Add', route('seasons.create'));
});
Breadcrumbs::register('seasons.edit', function($b) {
    $b->parent('seasons.index');
    $b->push('Edit');
});
Breadcrumbs::register('seasons.show', function($b) {
    $b->parent('seasons.index');
    $b->push('View');
});

// Data Management - Clubs
Breadcrumbs::register('clubs.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Clubs', route('clubs.index'));
});
Breadcrumbs::register('clubs.create', function($b) {
    $b->parent('clubs.index');
    $b->push('Add', route('clubs.create'));
});
Breadcrumbs::register('clubs.edit', function($b) {
    $b->parent('clubs.index');
    $b->push('Edit');
});
Breadcrumbs::register('clubs.show', function($b) {
    $b->parent('clubs.index');
    $b->push('View');
});

// Data Management - Venues
Breadcrumbs::register('venues.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Venues', route('venues.index'));
});
Breadcrumbs::register('venues.create', function($b) {
    $b->parent('venues.index');
    $b->push('Add', route('venues.create'));
});
Breadcrumbs::register('venues.edit', function($b) {
    $b->parent('venues.index');
    $b->push('Edit');
});
Breadcrumbs::register('venues.show', function($b) {
    $b->parent('venues.index');
    $b->push('View');
});

// Data Management - Roles
Breadcrumbs::register('roles.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Roles', route('roles.index'));
});
Breadcrumbs::register('roles.create', function($b) {
    $b->parent('roles.index');
    $b->push('Add', route('roles.create'));
});
Breadcrumbs::register('roles.edit', function($b) {
    $b->parent('roles.index');
    $b->push('Edit');
});
Breadcrumbs::register('roles.show', function($b) {
    $b->parent('roles.index');
    $b->push('View');
});

// Data Management - Divisions
Breadcrumbs::register('divisions.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Divisions', route('divisions.index'));
});
Breadcrumbs::register('divisions.create', function($b) {
    $b->parent('divisions.index');
    $b->push('Add', route('divisions.create'));
});
Breadcrumbs::register('divisions.edit', function($b) {
    $b->parent('divisions.index');
    $b->push('Edit');
});
Breadcrumbs::register('divisions.show', function($b) {
    $b->parent('divisions.index');
    $b->push('View');
});

// Data Management - Teams
Breadcrumbs::register('teams.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Teams', route('teams.index'));
});
Breadcrumbs::register('teams.create', function($b) {
    $b->parent('teams.index');
    $b->push('Add', route('teams.create'));
});
Breadcrumbs::register('teams.edit', function($b) {
    $b->parent('teams.index');
    $b->push('Edit');
});
Breadcrumbs::register('teams.show', function($b) {
    $b->parent('teams.index');
    $b->push('View');
});

// Data Management - Fixtures
Breadcrumbs::register('fixtures.index', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Fixtures', route('fixtures.index'));
});
Breadcrumbs::register('fixtures.create', function($b) {
    $b->parent('fixtures.index');
    $b->push('Add', route('fixtures.create'));
});
Breadcrumbs::register('fixtures.edit', function($b) {
    $b->parent('fixtures.index');
    $b->push('Edit');
});
Breadcrumbs::register('fixtures.show', function($b) {
    $b->parent('fixtures.index');
    $b->push('View');
});

// Data Management - Available Appointments
Breadcrumbs::register('available-appointments.index', function ($b) {
    $b->parent('admin::dataManagement');
    $b->push('Available appointments', route('available-appointments.index'));
});
Breadcrumbs::register('available-appointments.create', function ($b) {
    $b->parent('available-appointments.index');
    $b->push('Add', route('available-appointments.create'));
});
Breadcrumbs::register('available-appointments.edit', function ($b) {
    $b->parent('available-appointments.index');
    $b->push('Edit');
});
Breadcrumbs::register('available-appointments.show', function ($b) {
    $b->parent('available-appointments.index');
    $b->push('View');
});

// Data Management - Upload Fixtures
Breadcrumbs::register('uploadFixtures', function ($b) {
    $b->parent('admin::dataManagement');
    $b->push('Upload fixtures', route('uploadFixtures'));
});
// Data Management - Upload Fixtures - Staus
Breadcrumbs::register('uploadStatus', function ($b) {
    $b->parent('uploadFixtures');
    $b->push('Status');
});