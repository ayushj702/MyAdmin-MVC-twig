<?php

namespace Controller;

use Core\Response;
use Doctrine\ORM\EntityManagerInterface;
use Model\User;
use Model\Role;

class HomeController extends BaseController {
    

    public function render($request): Response {
        // Start session
        session_start();


        // Check if user is logged in
        if (isset($_SESSION['id'])) {
            $userData = $this->getUserData();
            $profilePhoto = $this->getProfilePhoto($_SESSION['id']);

            // Render home_view.twig with user data and profile photo
            $content = $this->twig->render('home_view.twig', [
                'userData' => $userData,
                'profilePhoto' => $profilePhoto,
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
            $roles = $currUser->getRoles()->toArray();
            
    
        
            // Return user data
            return [
                'name' => $name,
                'email' => $email,
                'age' => $age,
                'roles' => $roles,

            ];
        } else {
            // Return an empty array if user doesn't exist
            return [];
        }
    }

    private function getProfilePhoto($userId) {
        // Fetch profile photo path from the database
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->find($userId);

        // Check if user exists and has a profile photo
        if ($user && $user->getProfilePhoto()) {
            return $user->getProfilePhoto();
        }

        // Return default profile photo path if no photo is found
        return 'default_profile_photo.jpg';
    }



    
}
