<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group("api", function ($routes) {
    $routes->group("users", function ($routes) {
        $routes->get("/", "UserController::index");
        $routes->get("(:segment)", "UserController::show/$1");
        $routes->post("/", "UserController::store");
    });

    $routes->group("authentications", function ($routes) {
        $routes->post("/", "AuthenticationController::login");
        $routes->put("/", "AuthenticationController::refresh");
        $routes->delete("/", "AuthenticationController::logout");
    });

    $routes->get("test", "UserController::test");
});
