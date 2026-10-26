<?php
// Start the session to access session variables
session_start();

// Include your User model
require_once '../models/User.php'; // adjust the path to where your User.php file is located

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect if the user is not logged in
    header("Location: ../login.php");
    exit();
}

// Instantiate the User object
$userModel = new User();

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_changes'])) {
    // Get the values from the form
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validate the input (you can expand validation here as needed)
    if (empty($firstName) || empty($lastName) || empty($email)) {
        // Handle empty fields, e.g., show an error message
        echo "All fields are required!";
        exit();
    }

    try {
        // Update the user details
        $userModel->update($_SESSION['user_id'], $firstName, $lastName, $email);

        // Redirect to the profile page or show a success message
        header("Location: /Monora/views/account.php");
        exit();
    } catch (Exception $e) {
        // Handle any errors (e.g., database issues)
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>
