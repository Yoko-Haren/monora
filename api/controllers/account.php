<?php
session_start();
require_once '../models/User.php'; // adjust path if needed

$userModel = new User(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_changes'])) {
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $userId = $_SESSION['user_id'];

    $userModel->update($userId, $firstName, $lastName, $email); 
    // Refresh user data if needed
    $user = $userModel->getById($userId); // optional, if you need updated values
}
