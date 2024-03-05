<?php

namespace Controller;

use Core\Response;
use Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Model\User;

class ProfilePhotoController extends BaseController {

    public function render($request): Response {
        // Authentication
        session_start();
        if (!isset($_SESSION['id'])) {
            $response = new Response();
            $response->setRedirect("/denied");
            return $response;
        }

        if ($request->getMethod() === 'POST' && isset($_POST['submit'])) {
            return $this->handlePhotoUpload();
        } else {
            // Render the photo upload form
            return $this->generateUploadForm();
        }
    }

    private function handlePhotoUpload(): Response {
        if ($_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
            $errorMessage = "Failed to upload profile photo.";
            return $this->generateUploadForm($errorMessage);
        }

        $uploadDir = 'uploads/profile_photos/';
        $uploadFile = $uploadDir . basename($_FILES['profile_photo']['name']);
        if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadFile)) {
            $errorMessage = "Failed to move uploaded file.";
            return $this->generateUploadForm($errorMessage);
        }

        // Update user's profile photo path in the database
        $id = $_SESSION['id'];
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->find($id);
        if (!$user) {
            $errorMessage = "User not found.";
            return $this->generateUploadForm($errorMessage);
        }

        $user->setProfilePhoto($uploadFile);
        $this->entityManager->flush();

        // Redirect to home page
        $response = new Response();
        $response->setRedirect("/");
        return $response;
    }

    private function generateUploadForm($errorMessage = ""): Response {
        // Render the photo upload form
        $content = $this->twig->render('upload_photo.twig', ['errorMessage' => $errorMessage]);
        
        $response = new Response();
        $response->setContent($content);
        return $response;
    }
}
