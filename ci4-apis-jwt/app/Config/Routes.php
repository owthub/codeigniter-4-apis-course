<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Open Routes
$routes->post("/auth/register", "Api\AuthorController::registerAuthor");
$routes->post("/auth/login", "Api\AuthorController::loginAuthor"); // token value

// Protected APIs
$routes->group("author", ["namespace" => "App\Controllers\Api", "filter" => "jwt_auth"], function($routes){

    // Author Routes
    $routes->get("profile", "AuthorController::authorProfile");
    $routes->get("logout", "AuthorController::logoutAuthor");

    // Books Routes
    $routes->post("add-book", "BookController::createBook");
    $routes->get("list-book", "BookController::authorBooks");
    $routes->delete("delete-book/(:num)", "BookController::deleteAuthorBook/$1");
});