<?php

// src/Controller/LogoutController.php

namespace Controller;

use Core\Response;
use Model\Session;

class LogoutController extends BaseController
{
    /**
     * Render the view
     *
     * @param [type] $request
     * @return Response
     */
    public function render($request): Response
    {
        session_start();

        if ($this->checkSession()) {
            $userId = $_SESSION['id'];

            // Find session entry in the database
            $session = $this->entityManager->getRepository(Session::class)->findOneBy(['userId' => $userId]);

            if ($session) {
                // Remove session entry
                $this->entityManager->remove($session);
                $this->entityManager->flush();
            }
            session_destroy();
            $response = new Response();
            $response->setRedirect("/");

            return $response;
        }
        session_destroy();
        $response = new Response();
        $response->setRedirect("/");

        return $response;
    }
}
