<?php

namespace Controller;

use Core\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class NotFoundController extends BaseController {
    
    

    public function render($request): Response {
        $response = new Response();
        $content = $this->twig->render('not_found_view.twig');
        $response->setContent($content);

        return $response;
    }
}
