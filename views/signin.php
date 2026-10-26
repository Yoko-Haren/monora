<?php
// signin.php
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Monora</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/signin.css">
    
    <style>
        /* Active */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
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
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background: #ffffff;
            color: #000000;
        }
        
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
        
        @keyframes pulse {
            0%, 100% { opacity: 0.1; }
            50% { opacity: 0.4; }
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
                <a href="../views/signup.php"><i class="fas fa-user-plus"></i></a>
            </nav>
        </div>
    </header>


    <br>
    <!-- Background Lines -->
    <div id="lines-container"></div>

    <!-- Signin Form Container -->
    <div class="container">
        <h1>Welcome Back</h1>
        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>

        <form id="login-form" onsubmit="handleLoginSubmit(event)">
            <div class="error-message">
                <?php
                if (isset($_GET['error'])) {
                    echo htmlspecialchars($_GET['error']);
                }
                ?>
            </div>
            <input type="text" name="username_or_email" placeholder="Username or Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <div class="options">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <a href="forgot_password.php">Forgot password?</a>
            </div>

            <button class="submit" type="submit">Sign In</button>
        </form>

        <div class="divider">or continue with</div>

        <div class="social-login">
            <button class="social-btn">
                <i class="fab fa-google"></i> Google
            </button>
            <button class="social-btn">
                <i class="fab fa-facebook-f"></i> Facebook
            </button>
            <button class="social-btn">
                <i class="fab fa-apple"></i> Apple
            </button>
        </div>
    </div>
    <br>

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

        // Handle the login form submission
        function handleLoginSubmit(event) {
            event.preventDefault(); // Prevent the default form submission

            const formData = new FormData(document.getElementById('login-form'));

            fetch('../api/auth/signin_process.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // If login is successful, redirect to the dashboard
                    window.location.href = data.redirect;
                } else {
                    // Display error message if login fails
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            });
        }
    </script>
</body>
</html>
