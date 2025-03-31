<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group("api", function ($routes) {
    $routes->get("users", "UserController::index");
    $routes->get("users/(:segment)", "UserController::show/$1");
    $routes->post("users", "UserController::store");
    $routes->get("test", "UserController::test");
});
