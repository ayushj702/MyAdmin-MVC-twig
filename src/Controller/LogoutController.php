<?php

// src/Controller/LogoutController.php

namespace Controller;

use Core\Response;

class LogoutController extends BaseController {

    public function render($request): Response {
        session_start();
        session_destroy();
        $response = new Response();
            $response->setRedirect("/");

            return $response;
        
    }
}
