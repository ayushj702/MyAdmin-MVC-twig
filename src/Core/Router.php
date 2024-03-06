<?php

namespace Core;

use Controller\NotFoundController;

class Router {
    private $routes = [];

    public function addRoute($method, $path, $controller) {
        $this->routes[$method][$path] = $controller;
    }
    
    public function resolveRoute($request) {
        $url = $request->getUrl();
        //print_r($url);
        //die;


        $method = $request->getMethod();
        //print_r($url);
        
        //die;
        foreach ($this->routes as $methodKey => $routes) {
            foreach ($routes as $path => $controller) {
                //echo "Debug:<br> url: $url, path: $path, methodkey: $methodKey, method: $method, controller: $controller<br>";
                //die; // Debug statement
                //cho "$url and $path <br>";
                if ($url === $path) {
                    //echo "Debug: Match found for URL: $url<br>"; // Debug statement
                    //die;
                    return [
                        'controller' => $controller,
                        'methods' => [$method]
                    ];
                }
            }
        }
    
        // If no route matches, return the default 404 controller
        return [
            'controller' => NotFoundController::class,
            'methods' => ['GET', 'POST']
        ];
    }
    

}
