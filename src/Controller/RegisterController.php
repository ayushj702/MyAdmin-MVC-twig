<?php

namespace Controller;

use Core\Response;
use Core\Request;
use Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class RegisterController extends BaseController {

    public function render($request): Response {
        // Check if the user is already logged in
        if(isset($_SESSION['id'])) {
            $response = new Response();
            $response->setRedirect("/");
            return $response;
        }

        // Handle form submission
        if ($request->getMethod() === 'POST' && isset($_POST['submit'])) {
            return $this->handleRegistration($request);
        } else {
            // Render the registration form
            $content = $this->twig->render('register_view.twig');
            $response = new Response();
            $response->setContent($content);
            return $response;
        }
    }

    private function handleRegistration(Request $request): Response {
        // Retrieve form data
        $name = $_POST['name'];
        $email = $_POST['email'];
        $age = $_POST['age'];
        $password = $_POST['password'];

        // Check if email already exists
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if($existingUser) {
            $errorMessage = "This email address is already in use. Please try another.";
            $content = $this->twig->render('register_view.twig', ['errorMessage' => $errorMessage]);
            $response = new Response();
            $response->setContent($content);
            return $response;
        } else {
            // Insert user into database
            $user = new User();
            $user->setName($name);
            $user->setEmail($email);
            $user->setAge($age);
            $user->setPassword($password);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $response = new Response();
            $response->setRedirect("/login");
            return $response;
        }
    }
}