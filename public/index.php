<?php
session_start();



require_once ('../core/BaseController.php');
require_once '../core/Router.php';
require_once '../core/Route.php';
require_once '../app/controllers/HomeController.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/AdminController.php';
require_once '../app/controllers/ClientController.php';
require_once '../app/config/db.php';



$router = new Router();
Route::setRouter($router);



// Define routes
// auth routes 
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'handleRegister']);
Route::get('/', [AuthController::class, 'showleLogin']);
Route::get('/login', [AuthController::class, 'showleLogin']);
Route::post('/login', [AuthController::class, 'handleLogin']);
Route::post('/logout', [AuthController::class, 'logout']);

// admin routers

Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/users', [AdminController::class, 'handleUsers']);
Route::get('/admin/categories', [AdminController::class, 'showCats']);
Route::get('/admin/categories', [AdminController::class, 'categories']);
Route::get('/admin/categories/addmodcat', [AdminController::class, 'addCatMod']);
Route::get('/admin/categories/addmodsubcat', [AdminController::class, 'addSubCatMod']);
Route::get('/admin/categories/deletecat', [AdminController::class, 'deleteCat']);
Route::get('/admin/categories/deletesubcat', [AdminController::class, 'deleteSubCat']);
Route::get('/admin/testimonials', [AdminController::class, 'testimonials']);
Route::get('/admin/testimonials/remove', [AdminController::class, 'removeTesti']);
Route::get('/admin/projects', [AdminController::class, 'projectsMethod']);
Route::get('/admin/projects/remove', [AdminController::class, 'removePro']);
Route::get('/admin/users/remove', [AdminController::class, 'removeUser']);
Route::get('/admin/users/block', [AdminController::class, 'blockUser']);




// end admin routes 

// client routers 
Route::get('/client', [ClientController::class, 'index']);
Route::get('/client/projects', [ClientController::class, 'projects']);
Route::get('/client/projects/remove', [ClientController::class, 'removePro']);
Route::get('/client/projects/addmodproject', [ClientController::class, 'addModPro']);
Route::get('/client/offers', [ClientController::class, 'clientOffer']);
Route::get('/client/offers/accept', [ClientController::class, 'acceptOffer']);
Route::get('/client/offers/addTesti', [ClientController::class, 'addTesti']);
Route::get('/client/testimonials', [ClientController::class, 'testimonnialsClinet']);
Route::get('/client/testimonials/edit', [ClientController::class, 'addTesti']);
Route::get('/client/testimonials/remove', [ClientController::class, 'removeTestimonial']);
// client Routes 
// Route::get('/client/dashboard', [ClientController::class, 'index']);



// Dispatch the request
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);



