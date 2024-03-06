<?php

namespace Controller;

use Core\Response;
use Doctrine\ORM\EntityManagerInterface;
use Model\User;
use Model\Role;

class RoleController extends BaseController {

    public function render($request): Response {
        session_start();

        // Check if user is logged in
        if (isset($_SESSION['id'])) {
            $userRepo = $this->entityManager->getRepository(User::class);
            $user = $userRepo->find($_SESSION['id']);

            $adminRole = new Role();
            $adminRole->setName('administrator');
            $this->entityManager->persist($adminRole);
            $this->entityManager->flush();

            $user->setRole($adminRole);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        // Redirect back to the home page
        $response = new Response();
            $response->setRedirect("/");
            return $response;
    }
}
