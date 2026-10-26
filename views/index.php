<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Validator</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        input[type=email] { padding: 10px; width: 300px; }
        input[type=submit] { padding: 10px 20px; }
        form { margin-bottom: 30px; }
    </style>
</head>
<body>

<h1>Check if an Email is Valid</h1>

<form action="validate.php" method="GET">
    <label for="email">Enter Email:</label><br>
    <input type="email" name="email" id="email" required>
    <input type="submit" value="Validate">
</form>

</body>
</html>
