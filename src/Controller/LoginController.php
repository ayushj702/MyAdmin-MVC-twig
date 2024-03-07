<?php

namespace Controller;

use Core\Response;
use Doctrine\ORM\EntityManagerInterface;
use Model\User;
use Model\Session;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class LoginController extends BaseController {
    
    

    public function render($request): Response {
        // Check if the user is already logged in
        session_start();
        if(isset($_SESSION['id'])) {
            $response = new Response();
            $response->setRedirect("/");
            return $response;
        }

        // Handle form submission
        if ($request->getMethod() === 'POST' && isset($_POST['submit'])) {
            return $this->handleLogin($request);
        } elseif ($request->getMethod() === 'GET') {
            // Render the login form
            $content = $this->twig->render('login_view.twig');
            $response = new Response();
            $response->setContent($content);
            return $response;
        }
    }

    private function handleLogin($request): Response {
        // Retrieve form data
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if the email exists
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Validate the password
        if ($user && md5($password) === $user->getPassword()) {
            // Start the session and set user data
            session_start();
            $_SESSION['id'] = $user->getId();

            // Store session id in sessionDB
            $session = new Session();
            $session->setId(session_id());
            $session->setUserId($user->getId());
            $dataArray = [
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3'
            ];
            $dataString = serialize($dataArray);
            $session->setData($dataString); 
            $this->entityManager->persist($session);
            $this->entityManager->flush();

            // Using json_encode:
            // $dataString = json_encode($dataArray);


            // Redirect to home page
            $response = new Response();
            $response->setRedirect("/");
            return $response;
        } else {
            // Invalid credentials, display error message
            $error = "User does not exist or invalid credentials.";
            $content = $this->twig->render('login_view.twig', ['error' => $error]);
            $response = new Response();
            $response->setContent($content);
            return $response;
        }
    }
}