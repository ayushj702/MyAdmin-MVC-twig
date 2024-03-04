<?php

require_once 'bootstrap.php';
require __DIR__ . '/src/Core/Router.php';
require __DIR__ . '/src/Core/Request.php';

use Controller\AccessDenied;
use Core\Router;
use Core\Request;
use Core\Response;
use Controller\HomeController;
use Controller\LoginController;
use Controller\LogoutController;
use Controller\RegisterController;
use Controller\EditController;
use Controller\NotFoundController;

// Instantiate the router
$router = new Router();

// Define routes
$router->addRoute('GET', '/', HomeController::class);
$router->addRoute('POST', '/login', LoginController::class);
$router->addRoute('GET', '/logout', LogoutController::class);
$router->addRoute('POST', '/register', RegisterController::class);
$router->addRoute('POST', '/edit', EditController::class);
$router->addRoute('GET', '/denied', AccessDenied::class);

// Resolve the current route
$request = new Request($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
$route = $router->resolveRoute($request);
$controllerClass = $route['controller'];

$controllerObject = new $controllerClass();

$response = $controllerObject->render($request);


// Output the response
$response->send();
