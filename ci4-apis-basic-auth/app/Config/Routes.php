<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// API Routes
$routes->group("product", ["namespace" => "App\Controllers\Api", "filter" => "basic_auth"], function($routes){

    // [POST] - Add Product
    $routes->post("add", "ProductController::addProduct");

    // [GET] - List Produdcts
    $routes->get("list", "ProductController::listAllProducts");

    // [GET] - To Get Single Product Data
    $routes->get("(:num)", "ProductController::getSingleProduct/$1");

    // [PUT] - To Udpate Product Data
    $routes->put("(:num)", "ProductController::updateProduct/$1");

    // [DELETE] - To Delete Product Data
    $routes->delete("(:num)", "ProductController::deleteProduct/$1");
});