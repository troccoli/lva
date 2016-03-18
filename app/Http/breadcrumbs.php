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

Breadcrumbs::register('admin::dataManagement::seasons', function($b) {
    $b->parent('admin::dataManagement');
    $b->push('Seasons', route('admin::dataManagement::seasons'));
});