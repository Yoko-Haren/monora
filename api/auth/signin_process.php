<?php
// api/auth/signin_process.php

header('Content-Type: application/json');
session_start();

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.");
    }

    $usernameOrEmail = $_POST['username_or_email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($usernameOrEmail) || empty($password)) {
        throw new Exception("Username/Email and password are required.");
    }

    require_once '../Database.php';
    require_once '../models/User.php';

    $user = new User();
    $userData = $user->findByUsernameOrEmail($usernameOrEmail);

    if (!$userData || !password_verify($password, $userData['password_hash'])) {
        throw new Exception("Invalid login credentials.");
    }

    // Set session for user ID
    $_SESSION['user_id'] = $userData['user_id'];
    
    // Respond with success and redirect URL
    $response['success'] = true;
    $response['message'] = "Login successful!";
    $response['redirect'] = '../views/dashboard.php';  // Or handle redirect on client-side

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit();
