<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group("api", function ($routes) {
    $routes->get("users", "UserController::index");
    $routes->get("userstest", "TestApiController::index");
    $routes->get("testerror", "TestApiController::testerror");
});
