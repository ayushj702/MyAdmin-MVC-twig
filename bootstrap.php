<?php
// bootstrap.php

require_once "vendor/autoload.php";

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

$dbParams = [
    'driver'   => 'pdo_mysql',
    'user'     => 'db',
    'password' => 'db',
    'dbname'   => 'db',
    'host' => 'db',
    'port' => 3306,
];

$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__."/src/Model"], // Adjusted path to match the namespace
    isDevMode: true,
);

// Configuring the database connection
$connection = DriverManager::getConnection($dbParams, $config);

// Obtaining the entity manager
$entityManager = new EntityManager($connection, $config);
?>
