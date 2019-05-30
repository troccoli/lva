<?php

use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

Breadcrumbs::for('home', function (BreadcrumbsGenerator $trail) {
    $trail->push('Home', '/');
});
Breadcrumbs::for('dashboard', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Dashboard', '/dashboard');
});
Breadcrumbs::for('login', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Login', '/login');
});
Breadcrumbs::for('register', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Register', '/register');
});
Breadcrumbs::for('password.request', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Reset password', '/password/reset');
});
