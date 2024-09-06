<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Api\StudentController;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// API Routes
$routes->group("api", ["namespace" => "App\Controllers\Api"], function($routes){

    // Add Student API
    $routes->post("create-student", [StudentController::class, "addStudent"]);

    // List Students API
    $routes->get("students", [StudentController::class, "listStudents"]);

    // Single Student Data API
    $routes->get("student/(:num)", [StudentController::class, "getSingleStudentData"]);

    // Update Student API
    $routes->put("student/(:num)", [StudentController::class, "updateStudent"]);;

    // Delete Student API
    $routes->delete("student/(:num)", [StudentController::class, "deleteStudent"]);
});