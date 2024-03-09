<?php

namespace Controller;

use Core\Response;
use Model\Permission;
use Model\User;
use Model\Role;
use Model\Session;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AdminController extends BaseController {
    protected $permissionManager;
    protected $entityManager;
    protected $twig;

    public function __construct() {
        $this->permissionManager = new Permission();
        global $entityManager;
        $this->entityManager = $entityManager;
        $loader = new FilesystemLoader(__DIR__ . '/../View');
        $this->twig = new Environment($loader);
    }

    public function render($request): Response {
        session_start();
        //dd($_SESSION['user_roles']);
        // Check if the user is an administrator
        if ($this->isAdmin()) {
            $activeUsers = $this->entityManager->getRepository(User::class)->findAll();
            $activeUserRoles = $_SESSION['user_roles'];
            // Render dashboard view
            $content = $this->twig->render('admin_dashboard.twig', [
                'activeUsers' => $activeUsers,
                'activeUserRoles' => $activeUserRoles,
            ]);
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
        // Check if the user roles are stored in the session
        if (isset($_SESSION['user_roles'])) {
            foreach ($_SESSION['user_roles'] as $role) {
                // Check if the role is 'administrator'
                if ($role === 'administrator') {
                    return true; // User has the role of administrator
                }
            }
        }
        return false; // 'Administrator' role not found in the user roles
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

}
