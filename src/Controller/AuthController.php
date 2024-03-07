<?php

namespace Controller;

use Core\Response;
use Model\User;

class AuthController extends BaseController {

    public function render($request): Response {
        // Check if the user is an administrator
        if ($this->isAdmin()) {
            $activeUsers = $this->entityManager->getRepository(User::class)->findAll();

            // Render dashboard view
            $response = new Response();
            $response->setRedirect("/");
            return $response;
        } else {
            $response = new Response();
            $response->setRedirect("/");
            return $response;
        }
    }

    private function isAdmin() {
        // for now return to true to check
        return true;
    }

    public function deleteUser($request): Response {
        if ($this->isAdmin()) {
            $userId = $request->getParameter('id');

            // Find the user by ID
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            // If the user exists, delete it
            if ($user) {
                $this->entityManager->remove($user);
                $this->entityManager->flush();
            }

            $response = new Response();
            $response->setRedirect("/admin");
            return $response;
        } else {
            $response = new Response();
            $response->setRedirect("/");
            return $response;
        }
    }

    //public function editUser($request): Response {}
    //public function editRole($request): Response {}
    //public function deleteRole($request): Response {}

}
