<?php

namespace Controller;

use Core\Response;
use Doctrine\ORM\EntityManagerInterface;
use Model\User;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class BaseController {
    protected $entityManager;
    protected $twig;

    public function __construct() {
        global $entityManager;
        $this->entityManager = $entityManager;
        $loader = new FilesystemLoader(__DIR__ . '/../View');
        $this->twig = new Environment($loader);
    }

    abstract public function render($request): Response;

    
}

//use this class for methods which are to be used in other controllers frequently