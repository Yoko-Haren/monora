<?php
// Set environment: 'development' or 'production'
define('ENVIRONMENT', 'development');

// (Optional) Turn on full error reporting in development
if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

$host = 'localhost';       // or 127.0.0.1
$dbname = 'monora';  // change this
$username = 'root';        // XAMPP default
$password = '';            // XAMPP default

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
?>