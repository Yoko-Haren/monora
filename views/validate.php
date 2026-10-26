<?php
// Check if email was provided
if (!isset($_GET['email'])) {
    header("Location: index.php");
    exit;
}

$email = $_GET['email'];
require_once __DIR__ . '/../api/core/secrets.php'; // holds the ValidEmail.net token, kept out of git
$token = VALIDEMAIL_TOKEN;
$url = "https://api.ValidEmail.net/?email=" . urlencode($email) . "&token=" . $token;

// Fetch API response
$response = file_get_contents($url);
$data = json_decode($response, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Validation Result</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .result { padding: 20px; border: 1px solid #ccc; background-color: #f9f9f9; width: 400px; }
        .back { margin-top: 20px; display: inline-block; text-decoration: none; color: #007BFF; }
    </style>
</head>
<body>

<h1>Email Validation Result</h1>

<div class="result">
    <?php if ($data && isset($data['status'])): ?>
        <p><strong>Email:</strong> <?= htmlspecialchars($data['email']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($data['status']) ?></p>
    <?php else: ?>
        <p>Invalid or no response from the API.</p>
    <?php endif; ?>
</div>

<a class="back" href="index.php">← Back</a>

</body>
</html>
