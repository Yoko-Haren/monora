<?php
session_start();
require_once '../api/core/config.php';
require_once '../api/models/User.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$userModel = new User($pdo);
$user = $userModel->getUserById($_SESSION['user_id']); 

if (!$user) {
    header("Location: signin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Account - Monora</title>
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --bg-color: #fafafa;
            --text-color: #1a1a1a;
            --border-color: #e0e0e0;
            --card-bg: #ffffff;
            --hover-bg: #f0f0f0;
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.03);
            --shadow-md: 0 4px 15px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        
        .apple-header {
            background: rgba(250, 250, 250, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid var(--border-color);
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 22px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .apple-logo {
            font-size: 21px;
            font-weight: 600;
            color: var(--text-color);
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        
        .main-nav, .utility-nav {
            display: flex;
            gap: 24px;
        }
        
        .main-nav a, .utility-nav a {
            font-size: 14px;
            color: var(--text-color);
            text-decoration: none;
            transition: opacity 0.3s;
        }
        
        .main-nav a:hover, .utility-nav a:hover {
            opacity: 0.7;
        }
        
        .account-header {
            background: var(--bg-color);
            padding: 80px 0 40px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }
        
        .account-header h1 {
            font-size: 40px;
            font-weight: 600;
            margin: 0 0 8px;
            letter-spacing: -1px;
        }
        
        .account-header p {
            font-size: 18px;
            color: var(--text-color);
            opacity: 0.7;
            margin: 0;
            letter-spacing: 0.2px;
        }
        
        .account-grid {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 22px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .account-card {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 24px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
            border: 1px solid var(--border-color);
            position: relative;
            cursor: pointer;
            opacity: 0;
            transform: translateY(10px);
        }
        
        .account-card:hover {
            background: var(--hover-bg);
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
        
        .account-card::after {
            content: "›";
            position: absolute;
            top: 24px;
            right: 24px;
            font-size: 24px;
            opacity: 0.5;
            transition: opacity 0.2s ease;
        }
        
        .account-card:hover::after {
            opacity: 1;
        }
        
        .account-card h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 12px;
            color: var(--text-color);
            letter-spacing: -0.25px;
        }
        
        .account-card p {
            font-size: 15px;
            color: var(--text-color);
            opacity: 0.7;
            margin: 0 0 0px;
            min-height: 48px;
            line-height: 1.6;
        }

        /* Enhanced Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(8px);
            animation: fadeIn 0.3s ease-out;
        }
        
        .modal-content {
            background-color: var(--card-bg);
            border-radius: 18px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            transform: translateY(0);
            transition: transform 0.3s ease, opacity 0.3s ease;
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
        }
        
        .modal-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f5f5f7, #e0e0e0, #f5f5f7);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: 600;
            letter-spacing: -0.5px;
            position: relative;
            padding-left: 20px;
        }
        
        .modal-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 24px;
            width: 4px;
            background: var(--text-color);
            border-radius: 2px;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            opacity: 0.7;
            transition: all 0.2s ease;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .close-modal:hover {
            opacity: 1;
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .edit-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-color);
            opacity: 0.8;
            letter-spacing: 0.2px;
        }
        
        .edit-form input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 16px;
            font-family: inherit;
            background: var(--bg-color);
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            letter-spacing: 0.2px;
        }
        
        .edit-form input:focus {
            border-color: var(--text-color);
            outline: none;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }
        
        .edit-form button[type="submit"] {
            background: var(--text-color);
            color: var(--bg-color);
            border: none;
            border-radius: 10px;
            padding: 16px 24px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .edit-form button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }
        
        .edit-form button[type="submit"]:active {
            transform: translateY(0);
        }
        
        .apple-footer {
            background: var(--bg-color);
            padding: 60px 0 40px;
            border-top: 1px solid var(--border-color);
            margin-top: 60px;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 22px;
        }
        
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
            justify-content: center;
        }
        
        .footer-links a {
            color: var(--text-color);
            opacity: 0.7;
            text-decoration: none;
            font-size: 12px;
            transition: opacity 0.2s ease;
        }
        
        .footer-links a:hover {
            opacity: 1;
        }
        
        .copyright {
            color: var(--text-color);
            opacity: 0.5;
            font-size: 12px;
            text-align: center;
            margin-top: 20px;
        }

        /* Modal Styles */

        /* Payment Method Styles */
        .checkout-section {
            margin-top: 20px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .edit-link {
            font-size: 14px;
            color:rgb(0, 0, 0);
            cursor: pointer;
        }

        .payment-card {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            margin-bottom: 15px;
            transition: all 0.2s;
        }

        .payment-card.selected {
            border-color:rgb(0, 0, 0);
            background-color: rgba(0, 113, 227, 0.05);
        }

        .payment-icon {
            font-size: 24px;
            margin-right: 15px;
            color:rgb(0, 0, 0);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 113, 227, 0.1);
            border-radius: 50%;
        }

        .payment-details h4 {
            margin: 0 0 5px 0;
            font-weight: 500;
        }

        .payment-details p {
            margin: 0;
            color: #86868b;
            font-size: 14px;
        }

        /* Password Change Styles */
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 40px;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.2s;
        }

        .toggle-password:hover {
            opacity: 1;
        }

        .password-strength {
            margin-top: 8px;
        }

        .strength-meter {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-bottom: 4px;
            overflow: hidden;
        }

        .strength-meter::after {
            content: '';
            display: block;
            height: 100%;
            width: 0;
            background: #ff0000;
            transition: width 0.3s, background 0.3s;
        }

        .strength-text {
            font-size: 12px;
            color: #86868b;
        }

        /* Strength levels */
        [data-strength="1"] .strength-meter::after {
            width: 25%;
            background: #ff0000;
        }

        [data-strength="2"] .strength-meter::after {
            width: 50%;
            background: #ff9900;
        }

        [data-strength="3"] .strength-meter::after {
            width: 75%;
            background: #33cc33;
        }

        [data-strength="4"] .strength-meter::after {
            width: 100%;
            background: #009900;
        }

        /* Notification Styles */
        #notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            transition: all 0.3s ease;
        }

        .notification-hidden {
            opacity: 0;
            visibility: hidden;
            transform: translate(-50%, -20px);
        }

        .notification-visible {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, 0);
        }

        .notification-content {
            padding: 12px 24px;
            border-radius: 12px;
            background: var(--text-color);
            color: var(--bg-color);
            font-size: 14px;
            font-weight: 500;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notification-success {
            background: #34C759;
        }

        .notification-error {
            background: #FF3B30;
        }

        .notification-icon {
            font-size: 16px;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { 
                transform: translateY(20px);
                opacity: 0;
            }
            to { 
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .account-header h1 {
                font-size: 32px;
            }
            
            .account-header p {
                font-size: 16px;
            }
            
            .main-nav, .utility-nav {
                display: none;
            }
            
            .account-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                margin: 20px;
                padding: 30px;
            }
            
            .modal-title {
                font-size: 20px;
            }
            
            .edit-form input {
                padding: 12px 14px;
            }
        }
    </style>
</head>
<body>
    <div id="notification" class="notification-hidden">
        <div class="notification-content"></div>
    </div>

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
            <a href="../api/auth/signout.php"><i class="fas fa-sign-out-alt"></i></a>
            <a href="../views/account.php"><i class="fas fa-user"></i></a>
        </nav>
    </div>
</header>

<section class="account-header">
    <h1><?= htmlspecialchars($user['first_name']) ?> <?= htmlspecialchars($user['last_name']) ?></h1>
    <p>Manage your account settings and preferences</p>
</section>

<section class="account-grid">
    <div class="account-card" id="personal-info" onclick="openEditModal()">
        <h2>Personal Information</h2>
        <p id="personal-text">
            <?= htmlspecialchars($user['first_name']) ?> <?= htmlspecialchars($user['last_name']) ?><br>
            <?= htmlspecialchars($user['email']) ?>
        </p>
    </div>

    <div class="account-card" onclick="openPasswordModal()">
        <h2>Security</h2>
        <p>Change your password and manage security settings</p>
    </div>

    <div class="account-card" onclick="window.location.href='../views/orders.php'">
        <h2>Orders</h2>
        <p>View order history and track shipments</p>
    </div>

    <div class="account-card" onclick="openPaymentModal()">
        <h2>Payment Methods</h2>
        <p>Add or edit payment options for faster checkout</p>
    </div>

    <div class="account-card" onclick="window.location.href='devices.php'">
        <h2>Your Devices</h2>
        <p>Manage devices connected to your account</p>
    </div>

    <div class="account-card" onclick="window.location.href='support.php'">
        <h2>Support</h2>
        <p>Get help with your account or orders</p>
    </div>
</section>

<!-- Enhanced Edit Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Personal Information</h3>
            <button class="close-modal" onclick="closeEditModal()">&times;</button>
        </div>
        <form class="edit-form" method="POST" action="../api/auth/update_user.php">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="First Name" 
                       value="<?= isset($user['first_name']) ? htmlspecialchars($user['first_name']) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Last Name" 
                       value="<?= isset($user['last_name']) ? htmlspecialchars($user['last_name']) : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Email" 
                       value="<?= isset($user['email']) ? htmlspecialchars($user['email']) : '' ?>" required>
            </div>
            
            <button type="submit" name="save_changes">Save Changes</button>
        </form>
    </div>
</div>

<!-- Payment Methods Modal -->
<div class="modal-overlay" id="paymentModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Payment Methods</h3>
            <button class="close-modal" onclick="closePaymentModal()">&times;</button>
        </div>
        
        <!-- Payment Method Section -->
        <div class="checkout-section">
            <div class="section-header">
                <h2 class="section-title">Payment Method</h2>
                <span class="edit-link">Edit</span>
            </div>
            
            <div class="payment-card selected">
                <div class="payment-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="payment-details">
                    <h4>Cash on Delivery</h4>
                    <p>Pay when you receive your order</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Change Modal -->
<div class="modal-overlay" id="passwordModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Change Password</h3>
            <button class="close-modal" onclick="closePasswordModal()">&times;</button>
        </div>
        <form class="edit-form" method="POST" action="../api/auth/change_password.php">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('current_password')"></i>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('new_password')"></i>
                <div class="password-strength">
                    <div class="strength-meter"></div>
                    <div class="strength-text"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('confirm_password')"></i>
            </div>
            
            <button type="submit" name="change_password">Change Password</button>
        </form>
    </div>
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
                Copyright © 2025 Monora Inc. All rights reserved.
            </div>
        </div>
    </div>
</footer>

<script>
// Modal functions
function openEditModal() {
    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.querySelector('.modal-content').style.opacity = '1';
        modal.querySelector('.modal-content').style.transform = 'translateY(0)';
    }, 10);
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.querySelector('.modal-content').style.opacity = '0';
    modal.querySelector('.modal-content').style.transform = 'translateY(20px)';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target === modal) {
        closeEditModal();
    }
}

// Animate account cards on load
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.account-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.animation = `cardEntrance 0.5s ease-out ${index * 0.1}s forwards`;
        }, 0);
    });
});

// Add these functions to your existing script
function openPaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.querySelector('.modal-content').style.opacity = '1';
        modal.querySelector('.modal-content').style.transform = 'translateY(0)';
    }, 10);
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.querySelector('.modal-content').style.opacity = '0';
    modal.querySelector('.modal-content').style.transform = 'translateY(20px)';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Update the window.onclick function
window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    const paymentModal = document.getElementById('paymentModal');
    
    if (event.target === editModal) {
        closeEditModal();
    }
    if (event.target === paymentModal) {
        closePaymentModal();
    }
}

// Add these functions to your existing script
function openPasswordModal() {
    const modal = document.getElementById('passwordModal');
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.querySelector('.modal-content').style.opacity = '1';
        modal.querySelector('.modal-content').style.transform = 'translateY(0)';
    }, 10);
}

function closePasswordModal() {
    const modal = document.getElementById('passwordModal');
    modal.querySelector('.modal-content').style.opacity = '0';
    modal.querySelector('.modal-content').style.transform = 'translateY(20px)';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function togglePasswordVisibility(id) {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Password strength checker
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strengthMeter = document.querySelector('.strength-meter');
    const strengthText = document.querySelector('.strength-text');
    let strength = 0;
    
    // Check length
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    
    // Check for mixed case
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    
    // Check for numbers and special chars
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    // Cap at 4
    strength = Math.min(strength, 4);
    
    // Update UI
    strengthMeter.parentElement.setAttribute('data-strength', strength);
    
    // Update text
    const messages = ['Very Weak', 'Weak', 'Moderate', 'Strong', 'Very Strong'];
    strengthText.textContent = messages[strength];
});

// Update window.onclick to handle password modal
window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    const paymentModal = document.getElementById('paymentModal');
    const passwordModal = document.getElementById('passwordModal');
    
    if (event.target === editModal) {
        closeEditModal();
    }
    if (event.target === paymentModal) {
        closePaymentModal();
    }
    if (event.target === passwordModal) {
        closePasswordModal();
    }
}

// Add these functions to your existing script
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    const content = notification.querySelector('.notification-content');
    
    // Set content and style
    content.textContent = message;
    content.className = 'notification-content';
    content.classList.add(`notification-${type}`);
    
    // Add icon
    const icon = document.createElement('i');
    icon.className = `notification-icon fas ${
        type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'
    }`;
    content.prepend(icon);
    
    // Show notification
    notification.className = 'notification-visible';
    
    // Hide after 5 seconds
    setTimeout(() => {
        notification.className = 'notification-hidden';
    }, 5000);
}

// Close notification when clicked
document.getElementById('notification').addEventListener('click', function() {
    this.className = 'notification-hidden';
});

// Update your existing password form submission
document.querySelector('#passwordModal form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Password changed successfully!', 'success');
            closePasswordModal();
            this.reset(); // Reset form fields
        } else {
            showNotification(data.message || 'Failed to change password', 'error');
        }
    })
    .catch(error => {
        showNotification('An error occurred. Please try again.', 'error');
        console.error('Error:', error);
    });
});

</script>

</body>
</html>