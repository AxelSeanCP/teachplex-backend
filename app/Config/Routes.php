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

    $routes->group("courses", function ($routes) {
        $routes->post("/", "CourseController::store", ["filter" => ["auth", "admin"]]);
        $routes->get("/", "CourseController::index");
        $routes->get("(:segment)", "CourseController::show/$1");
        $routes->put("(:segment)", "CourseController::update/$1", ["filter" => ["auth", "admin"]]);
        $routes->delete("(:segment)", "CourseController::destroy/$1", ["filter" => ["auth", "admin"]]);
        $routes->post("(:segment)/finish", "CourseController::finish/$1", ["filter" => "auth"]);

        $routes->group("(:segment)/lessons", function ($routes) {
            $routes->post("/", "LessonController::store/$1", ["filter" => ["auth", "admin"]]);
            $routes->get("(:segment)", "LessonController::show/$1/$2", ["filter" => "auth"]);
            $routes->put("(:segment)", "LessonController::update/$1/$2", ["filter" => ["auth", "admin"]]);
            $routes->delete("(:segment)", "LessonController::destroy/$1/$2", ["filter" => ["auth", "admin"]]);
        });
    });

    $routes->group("enrollments", ["filter" => "auth"], function ($routes) {
        $routes->post("/", "EnrollmentController::store");
        $routes->get("/", "EnrollmentController::index");
        $routes->get("all", "EnrollmentController::all", ["filter" => "admin"]);
        $routes->delete("(:segment)", "EnrollmentController::destroy/$1", ["filter" => "admin"]);
    });

    $routes->group("certificates", function ($routes) {
        $routes->post("templates/upload", "CertificateController::upload", ["filter" => ["auth", "admin"]]);
        $routes->post("/", "CertificateController::store", ["filter" => "auth"]);
        $routes->get("download/(:any)", "CertificateController::download/$1");
        $routes->get("/", "CertificateController::index");
        $routes->get("(:segment)/verify", "CertificateController::show/$1");
        // $routes->delete("(:segment)", "CertificateController::remove/$1", ["filter" => "auth"]);
    });

    // CORS preflight handler for all /api/* requests
    $routes->options('(:any)', static function () {
        return;
    });
});
