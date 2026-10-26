<?php
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account - Monora</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Apple System Font Stack */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background: #ffffff;
            color: #000000;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        /* Apple-style Header */
        .apple-header {
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .apple-logo {
            font-size: 21px;
            font-weight: 600;
            color: #000;
            text-decoration: none;
        }
        
        /* Navigation */
        .main-nav {
            display: flex;
            gap: 25px;
            margin: 0 auto;
        }
        
        .main-nav a {
            color: #000;
            text-decoration: none;
            font-size: 14px;
            font-weight: 400;
            letter-spacing: -0.01em;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        
        .main-nav a:hover {
            opacity: 1;
            text-decoration: none;
        }
        
        .utility-nav {
            display: flex;
            gap: 20px;
        }
        
        .utility-nav a {
            color: #000;
            text-decoration: none;
            font-size: 14px;
            font-weight: 400;
            
        }
        
        /* Background Lines Design */
        #lines-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.3;
        }
        
        .line {
            position: absolute;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.1), rgb(0, 0, 0), rgba(0, 0, 0, 0.12));
            height: 1px;
            transform-origin: left center;
            animation: pulse 5s infinite ease-in-out;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.1; }
            50% { opacity: 0.4; }
        }
        
        /* Main Content */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        /* Form Styling */
        .container {
            position: relative;
            z-index: 1;
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #eeeeee;
        }
        
        h1 {
            text-align: center;
            color: #333333;
            margin-bottom: 20px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        
        p {
            text-align: center;
            margin-bottom: 25px;
            color: #555555;
            font-size: 15px;
        }
        
        a {
            color: #0066cc;
            text-decoration: none;
            font-weight: 500;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .name-fields {
            display: flex;
            gap: 15px;
        }
        
        .name-fields > div {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        input, select {
            padding: 12px 15px;
            border: 1px solid #dddddd;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f9f9f9;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #999999;
            background-color: #ffffff;
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.05);
        }
        
        .date-selectors {
            display: flex;
            gap: 10px;
        }
        
        .date-selectors select {
            flex: 1;
        }
        
        hr {
            border: none;
            border-top: 1px solid #eeeeee;
            margin: 20px 0;
        }
        
        small {
            display: block;
            font-size: 13px;
            color: #666666;
            margin-bottom: 5px;
            line-height: 1.4;
        }
        
        .checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 15px 0;
            font-size: 14px;
            color: #555555;
        }
        
        .checkbox input {
            margin-top: 3px;
        }
        
        .submit {
            background-color: #000000;
            color: white;
            padding: 14px;
            border: 1px solid #000000;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.2px;
        }
        
        .submit:hover {
            background-color: #ffffff;
            color: #000000;
        }
        
        input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #000000;
        }
        
        label {
            font-size: 14px;
            color: #555555;
            margin-bottom: 5px;
            display: block;
            font-weight: 500;
        }
        
        /* Apple-style Footer */
        .apple-footer {
            background-color: #f5f5f7;
            color: #86868b;
            font-size: 12px;
            padding: 20px 0;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 22px;
        }
        
        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .footer-links {
            display: flex;
            gap: 20px;
            margin-bottom: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .footer-links a {
            color: #515154;
            text-decoration: none;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .copyright {
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <header class="apple-header">
        <div class="header-container">
            <a href="../views/dashboard.php" class="apple-logo">Monora</a>
            <nav class="main-nav">
                <a href="#">Store</a>
                <a href="#">New Arrivals</a>
                <a href="#">Sales</a>
                <a href="#">Support</a>
            </nav>
            <nav class="utility-nav">
                <a href="../views/bag.php"><i class="fas fa-shopping-bag"></i></a>
                <a href="../views/signin.php"><i class="fas fa-sign-in-alt"></i></a>
            </nav>
        </div>
    </header>


    <!-- Background Lines -->
    <div id="lines-container"></div>

    <!-- Signup Form Container -->
    <div class="container">
        <h1>Create an Account</h1>
        <p>Finish signing up and enjoy 30% off your first order – limited time only!<br>
        Already have an account? <a href="../views/signin.php">Sign In</a></p>

        <br>

        <form action="../api/auth/signup_process.php" method="POST">
            <!-- Name fields in one line -->
            <div class="name-fields">
                <div>
                    <input type="text" name="first_name" placeholder="First Name" required>
                </div>
                <div>
                    <input type="text" name="last_name" placeholder="Last Name" required>
                </div>
            </div>

            <select name="country" required>
                <option value="">Select Country</option>
                <option value="Philippines">Philippines</option>
                <!-- You can add more countries here -->
            </select>

            <label>Birthday</label>
            <div class="date-selectors">
                <select name="birth_month" required>
                    <option value="">MM</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                    <?php endfor; ?>
                </select>

                <select name="birth_day" required>
                    <option value="">DD</option>
                    <?php for ($i = 1; $i <= 31; $i++): ?>
                        <option value="<?= $i ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                    <?php endfor; ?>
                </select>

                <select name="birth_year" required>
                    <option value="">YYYY</option>
                    <?php 
                    $year = date('Y');
                    for ($i = $year; $i >= 1900; $i--): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <hr>

            <input type="email" name="email" placeholder="Enter a valid email address" required>
            <input type="text" name="username" placeholder="Username" required>

            <small>Create a strong password with a mix of letters, numbers, and symbols to keep your account safe.</small>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>

            <div class="checkbox">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I certify that I am over 18 years old and have read the Monora <a href="#">Terms and Conditions</a>.</label>
            </div>

            <button type="submit" class="submit">Continue</button>
        </form>
    </div>

    <footer class="apple-footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Use</a>
                    <a href="#">Sales Policy</a>
                    <a href="#">Legal</a>
                    <a href="#">Site Map</a>
                </div>
                <div class="copyright">
                    Copyright © 2023 Monora Inc. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Background Lines -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('lines-container');
            const width = window.innerWidth;
            const height = window.innerHeight;
            const lineCount = 30;
            
            for (let i = 0; i < lineCount; i++) {
                const line = document.createElement('div');
                line.className = 'line';
                
                // Random properties for each line
                const top = Math.random() * height;
                const left = -50;
                const length = 100 + Math.random() * 200;
                const angle = -30 + Math.random() * 60;
                const delay = Math.random() * 5;
                const duration = 3 + Math.random() * 4;
                
                line.style.top = `${top}px`;
                line.style.left = `${left}px`;
                line.style.width = `${length}%`;
                line.style.transform = `rotate(${angle}deg)`;
                line.style.animationDelay = `${delay}s`;
                line.style.animationDuration = `${duration}s`;
                
                container.appendChild(line);
            }
        });

    document.querySelector('form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitBtn = form.querySelector('.submit');
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Processing...';
        
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = result.redirect;
            } else {
                alert(result.message || 'Registration failed');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Continue';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Continue';
        }
    });

    </script>
</body>
</html>