<?php
session_start();
require_once __DIR__ . '/../../api/core/config.php';  // Updated path
require_once __DIR__ . '/../../api/models/User.php';  // Updated path

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Initialize User model with database connection
$database = new Database();
$db = $database->connect();
$userModel = new User($db);

$userId = $_SESSION['user_id'];
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validate inputs
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
    exit;
}

if (strlen($newPassword) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}

// Get user data
$user = $userModel->getUserById($userId);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Verify current password
if (!password_verify($currentPassword, $user['password_hash'])) {
    echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
    exit;
}

// Update password
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
if ($userModel->updatePassword($userId, $hashedPassword)) {
    echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to change password']);
}