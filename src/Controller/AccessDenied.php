<?php

namespace Controller;

use Core\Response;
use Controller\BaseController;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AccessDenied extends BaseController {
    public function render($request): Response {
        //creating twig environment
        $loader = new FilesystemLoader(__DIR__ . '/../View');
        $twig = new Environment($loader);

        //rendering twig template
        $content = $twig->render('access_denied.twig', ['title' => 'Access Denied']);

        $response = new Response();
        $response->setContent($content);

        return $response;
    }
}
