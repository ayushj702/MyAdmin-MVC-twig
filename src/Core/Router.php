<?php

namespace Core;

use Controller\NotFoundController;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManager;
use Model\Session;
use Model\Role;
use Model\Permission;

class Router {
    private $routes = [];
    protected $entityManager;
    protected $role;
    protected $permission;

    public function __construct() {
        global $entityManager;
        $this->entityManager = $entityManager;
        $this->role = new Role();
        $this->permission = new Permission();
    }

    public function loadRoutesFromFile($file) {

        $routes = Yaml::parseFile($file);

        foreach ($routes['web'] as $routeName => $routeData) {
            $permission = $routeData['permission'] ?? 'default_permission';
            $this->addRoute(
                $permission,
                $routeData['method'],
                $routeData['path'],
                $routeData['controller'],
            );
        }
    }


    public function addRoute($permission, $methods, $path, $controller) {
        // default value if null
        // echo "method <br>";
        // var_dump($method);
        // echo "path <br>";
        // var_dump($path);
        // die;

        foreach($methods as $method){
            $this->routes[$method][$path] = [
                'controller' => $controller,
                'permission' => $permission,
            ];
        }

        
        
    }
    
    public function resolveRoute($request) {
        $url = $request->getUrl();
        $sessionOpen = $this->isSessionOpen();
        $method = $request->getMethod();
        if ($sessionOpen && isset($_SESSION['user_roles'])) {
            $userRoles = $_SESSION['user_roles'];
        } else {
            $userRoles = ['default']; // Assign the default role
        }

        foreach ($this->routes as $methodKey => $routes) {
            foreach ($routes as $path => $routeData) {

                if ($url === $path) {
                    $controller = $routeData['controller'];
                    $permission = $routeData['permission'];
                    
                    $allowedRole = $this->permission->applyRole($path);
                    $authenticated = $this->permission->applyAuth($path);

                    if ($allowedRole && $authenticated) {
                        return [
                            'controller' => $controller,
                            'methods' => [$method],
                            'sessionOpen' => $sessionOpen
                        ];
                    }
                }
            }
        }
    
        // If no route matches, return the default 404 controller
        return [
            'controller' => NotFoundController::class,
            'methods' => ['GET', 'POST'],
            'sessionOpen' => $sessionOpen
        ];
    }

    private function isSessionOpen() {
        // Retrieve session information from the database
        $session = $this->entityManager->getRepository(Session::class)->findOneBy(['id' => session_id()]);
        
        // Check if session exists and return true if it does
        return $session !== null;
    }
    

}
