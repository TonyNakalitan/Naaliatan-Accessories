<?php

require_once 'vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

// Database configuration
$dbParams = [
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => '',
    'dbname' => 'NAcessoriesDB',
    'host' => 'localhost',
];

// Create EntityManager
$config = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/src/Entity']);
$entityManager = EntityManager::create($dbParams, $config);

// Hash the password
$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);

// Create admin user
$sql = "INSERT INTO `user` (username, email, password, roles, created_at, display_name, is_active) 
          VALUES (:username, :email, :password, :roles, NOW(), :display_name, :is_active)";

$stmt = $entityManager->getConnection()->prepare($sql);
$stmt->execute([
    'username' => 'admin',
    'email' => 'admin@naaliatan.com',
    'password' => $hashedPassword,
    'roles' => json_encode(['ROLE_ADMIN']),
    'display_name' => 'Administrator',
    'is_active' => 1
]);

echo "Admin user created successfully!\n";
echo "Username: admin\n";
echo "Password: admin123\n";
echo "Email: admin@naaliatan.com\n";
