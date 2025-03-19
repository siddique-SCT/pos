<?php
declare(strict_types=1);

// Define connection constants
const DB_HOST = 'localhost';
const DB_NAME = 'sattioe1_pos7';
const DB_USER = 'sattioe1_pos';
const DB_PASS = 'sattioe1_pos';

try {
    // Create a PDO instance with PHP 8.2 features
    $pdo = new PDO(
        dsn: 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        username: DB_USER,
        password: DB_PASS,
        options: [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // Handle connection error with improved error handling
    error_log('Database Connection Error: ' . $e->getMessage());
    die('Database connection failed. Please check your configuration.');
}
?>
