<?php

namespace Controller;

use Core\Response;
use Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Model\User;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

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
            return $this->handleEditProfile();
        } else {
            // Render the edit profile form
            return $this->generateEditForm();
        }
    }

    private function handleEditProfile(): Response {
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

        // Update user data
        $user->setName($name);
        $user->setEmail($email);
        $user->setAge($age);

        // Persist changes to database
        $this->entityManager->flush();

        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['age'] = $age;

        // Redirect to home page
        $response = new Response();
        $response->setRedirect("/");
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