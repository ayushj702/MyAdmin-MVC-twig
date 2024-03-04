<?php

namespace Controller;

use Core\Response;
use Doctrine\ORM\EntityManagerInterface;
use Model\User;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class HomeController extends BaseController {
    

    public function render($request): Response {
        // Start session
        session_start();


        // Check if user is logged in
        if (isset($_SESSION['id'])) {
            $userData = $this->getUserData();
            // Render home_view.twig with user data
            $content = $this->twig->render('home_view.twig', [
                'userData' => $userData,
            ]);
        } else {
            // User is not logged in, render index_view.twig
            $content = $this->twig->render('index_view.twig');
        }

        $response = new Response();
        $response->setContent($content);
        
        // Return response with the rendered content
        return $response;
    }

    private function getUserData() {
        // Fetch user data from the database using EntityManager
        $currUser = $this->entityManager->getRepository(User::class)->find($_SESSION['id']);
        
        
        // Check if user exists
        if ($currUser) {
            // Get currUser data
            $name = $currUser->getName();
            $email = $currUser->getEmail();
            $age = $currUser->getAge();
        
            // Return user data
            return [
                'name' => $name,
                'email' => $email,
                'age' => $age
            ];
        } else {
            // Return an empty array if user doesn't exist
            return [];
        }
    }
    
}
