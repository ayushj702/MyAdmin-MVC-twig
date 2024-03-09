<?php

require_once 'bootstrap.php';
require __DIR__ . '/src/Core/Router.php';
require __DIR__ . '/src/Core/Request.php';

use Controller\AccessDenied;
use Controller\AdminController;
use Core\Router;
use Core\Request;
use Core\Response;
use Controller\HomeController;
use Controller\LoginController;
use Controller\LogoutController;
use Controller\RegisterController;
use Controller\EditController;
use Controller\NotFoundController;
use Controller\RoleController;
use Model\User;
use Model\Permission;

// Instantiate the router
$router = new Router();
$permissions = new Permission();

// Loading from Routes.yml
$router->loadRoutesFromFile('src/Config/Routes.yml');
//$permissions->loadPermissionsFromFile('src/Config/Permission.yml');


// Define routes
/*
$router->addRoute('GET', '/', HomeController::class);
$router->addRoute('POST', '/login', LoginController::class);
$router->addRoute('GET', '/logout', LogoutController::class);
$router->addRoute('POST', '/register', RegisterController::class);
$router->addRoute('POST', '/edit', EditController::class);
$router->addRoute('GET', '/denied', AccessDenied::class);
$router->addRoute('GET', '/change-role', RoleController::class);
$router->addRoute('GET', '/admin', AdminController::class);
 */



// Resolve the current route
$request = new Request($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
$route = $router->resolveRoute($request);
$controllerClass = $route['controller'];

$controllerObject = new $controllerClass();

// $dummyUser = new User();
// $dummyUser->setId(0); 
// $dummyUser->setName('anonymous');
// $dummyUser->setEmail('anon@anon.com');
// $dummyUser->setAge('1');
// $dummyUser->setPassword('Anonymous1!');




// $entityManager->persist($dummyUser);
// $entityManager->flush();

$response = $controllerObject->render($request);


// Output the response
$response->send();
