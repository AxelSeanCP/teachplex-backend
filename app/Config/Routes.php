<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group("api", ["filter" => "cors"], function ($routes) {
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

    $routes->group("enrollments", ["filter" => "auth"], function ($routes) {
        $routes->post("/", "EnrollmentController::store");
        $routes->get("/", "EnrollmentController::index");
        $routes->delete("/", "EnrollmentController::destroy");
    });

    $routes->group("certificates", function ($routes) {
        $routes->post("templates/upload", "CertificateController::upload");
        $routes->post("/", "CertificateController::store", ["filter" => "auth"]);
        $routes->get("download/(:any)", "CertificateController::download/$1");
        $routes->get("/", "CertificateController::index");
        $routes->get("(:segment)/verify", "CertificateController::show/$1");
        // $routes->delete("(:segment)", "CertificateController::remove/$1");
    });

    // CORS preflight handler for all /api/* requests
    $routes->options('(:any)', static function () {
        return;
    });
});
