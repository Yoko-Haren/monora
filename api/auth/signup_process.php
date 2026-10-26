<?php
// api/auth/signup_process.php

// Set content type
header('Content-Type: application/json');

// Define root path
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

// Include database configuration
require_once ROOT_PATH . '/api/Database.php';
require_once ROOT_PATH . '/api/models/User.php';

// Start session
session_start();

// Response array
$response = ['success' => false, 'message' => '', 'errors' => []];

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.");
    }

    // Get and sanitize form data
    $data = [
        'first_name' => filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING),
        'last_name' => filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING),
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
        'username' => filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING),
        'password' => $_POST['password'],
        'confirm_password' => $_POST['confirm_password'],
        'country' => filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING),
        'birth_date' => sprintf('%04d-%02d-%02d', 
            $_POST['birth_year'], 
            $_POST['birth_month'], 
            $_POST['birth_day']),
        'terms' => isset($_POST['terms']) ? true : false
    ];

    // Validate terms agreement
    if (!$data['terms']) {
        throw new Exception("You must agree to the terms and conditions.");
    }

    // Validate password match
    if ($data['password'] !== $data['confirm_password']) {
        throw new Exception("Passwords do not match.");
    }

    // Password strength validation
    if (strlen($data['password']) < 8) {
        throw new Exception("Password must be at least 8 characters long.");
    }

    // Create user
    $user = new User();
    $userId = $user->create($data);

    // ✅ Set user session after successful signup
    $_SESSION['user_id'] = $userId;

    // Generate session token
    $sessionToken = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days

    // Store session in database
    $db = (new Database())->connect();
    $stmt = $db->prepare("INSERT INTO sessions (session_id, user_id, ip_address, user_agent, expires_at) 
                         VALUES (:session_id, :user_id, :ip_address, :user_agent, :expires_at)");
    $stmt->execute([
        ':session_id' => $sessionToken,
        ':user_id' => $userId,
        ':ip_address' => $_SERVER['REMOTE_ADDR'],
        ':user_agent' => $_SERVER['HTTP_USER_AGENT'],
        ':expires_at' => $expiresAt
    ]);

    // Set session cookie
    setcookie('session_token', $sessionToken, time() + (30 * 24 * 60 * 60), '/', '', true, true);
    
    // Success response
    $response['success'] = true;
    $response['message'] = "Registration successful!";
    $response['redirect'] = '../views/dashboard.php';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    $response['errors'] = isset($errors) ? $errors : [];
}

// Return JSON response
echo json_encode($response);
exit();
