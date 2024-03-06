<?php

namespace Controller;

use Core\Response;
use Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Model\User;

class EditController extends BaseController {

    public function render($request): Response {
        // Authentication
        session_start();
        if (!isset($_SESSION['id'])) {
            $response = new Response();
            $response->setRedirect("/denied");
            return $response;
        }

        if ($request->getMethod() === 'POST' && isset($_POST['submit'])) {
            return $this->handleEditProfile($request);
        } else {
            // Render the edit profile form
            return $this->generateEditForm();
        }
    }

    private function handleEditProfile($request): Response {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $age = $_POST['age'];
        $id = $_SESSION['id'];

        // Validating form data
        if (empty($name) || empty($email) || empty($age) || empty($id)) {
            $errorMessage = "Please fill all fields.";
            return $this->generateEditForm($errorMessage);
        }

        // Retrieve user entity
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->find($id);

        if (!$user) {
            // Handle user not found
            $errorMessage = "User not found.";
            return $this->generateEditForm($errorMessage);
        }

        // Handle profile photo upload
        if (isset($_POST['submit'])) {
            $uploadDir = 'uploads/profile_photo/';
            
            $uploadFile = $uploadDir . basename($_FILES['profile_photo']['name']);
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadFile)) {
                // Update user's profile picture path
                $user->setProfilePhoto($uploadFile);
                $this->entityManager->flush();
                $response = new Response();
                $response->setRedirect("/");
                return $response;
            } else {
                // Handle upload failure
                $errorMessage = "Failed to upload profile picture.";
                return $this->generateEditForm($errorMessage);
            }
        }

        // Update user data
        $user->setName($name);
        $user->setEmail($email);
        $user->setAge($age);

        // Persist changes to database
        $this->entityManager->flush();

        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['age'] = $age;

        // Return JSON response indicating success
        $response = new Response();
        $response->setContent(json_encode(['success' => true]));
        $response->addHeader('Content-Type', 'application/json');
        return $response;
    }

    private function generateEditForm($errorMessage = ""): Response {
        // Render the edit profile form using Twig
        $content = $this->twig->render('edit_view.twig', ['errorMessage' => $errorMessage]);
        
        $response = new Response();
        $response->setContent($content);
        return $response;
    }
}
