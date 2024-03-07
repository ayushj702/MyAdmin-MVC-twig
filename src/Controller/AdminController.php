<?php

namespace Controller;

use Core\Response;
use Model\Permission;
use Model\User;
use Model\Role;

class AdminController extends BaseController {
    private $permissionManager;

    public function __construct() {
        $this->permissionManager = new Permission();
    }

    public function render($request): Response {
        session_start();
        // Check if the user is an administrator
        if ($this->isAdmin()) {
            $activeUsers = $this->entityManager->getRepository(User::class)->findAll();

            // Render dashboard view
            $content = $this->twig->render('admin_dashboard.twig', ['activeUsers' => $activeUsers]);
            $response = new Response();
            $response->setContent($content);
            return $response;
        } else {
            $response = new Response();
            $response->setRedirect("/");
            return $response;
        }
    }

    private function isAdmin() {
        
        $currUser = $this->entityManager->getRepository(User::class)->find($_SESSION['id']);
        
        $user = $this->entityManager->getRepository(User::class)->find($currUser);

        if ($currUser && $currUser->getRoles()) {
            foreach ($currUser->getRoles() as $role) {
                // Check if the role name is 'administrator'
                if ($role->getName() === 'administrator') {
                    return true; 
                }
            }
        }

        return false; // User does not have the role of administrator
    }

    public function deleteUser($request): Response {
        if ($this->isAdmin()) {
            $userId = $request->getParameter('id');
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            // Check if the user has permission to delete users
            if ($this->permissionManager->canDeleteUser($user)) {
                // If the user exists, delete it
                if ($user) {
                    $this->entityManager->remove($user);
                    $this->entityManager->flush();
                }
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

    // Implement editUser, editRole, deleteRole functions similarly
}
