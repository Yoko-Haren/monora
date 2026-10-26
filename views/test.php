<?php
session_start();
require_once '../api/core/config.php';
require_once '../api/models/User.php';
require_once '../api/models/Order.php';

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

// Redirect to dashboard if cart is empty or doesn't exist
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: dashboard.php');
    exit;
}

$userModel = new User($pdo);
$user = $userModel->getUserById($_SESSION['user_id']);

// Calculate totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['details']['price'] * $item['quantity'];
}
$tax = $subtotal * 0.08;
$total = $subtotal + $tax;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    try {
        $pdo->beginTransaction();
        
        // Prepare order data
        $shippingAddress = $_POST['street_address'] . "\n" . 
                          (!empty($_POST['apt_suite']) ? $_POST['apt_suite'] . "\n" : "") . 
                          $_POST['city'] . ", " . $_POST['state'] . " " . $_POST['zip_code'] . "\n" . 
                          $_POST['country'];
        
        $paymentMethod = $_POST['payment_method'];
        
        // Call stored procedure
        $stmt = $pdo->prepare("CALL place_order(?, ?, ?, @order_id)");
        $stmt->execute([$_SESSION['user_id'], $shippingAddress, $paymentMethod]);
        
        // Get the order ID
        $orderId = $pdo->query("SELECT @order_id AS order_id")->fetch(PDO::FETCH_ASSOC)['order_id'];
        
        $pdo->commit();
        
        // Clear the cart and redirect to confirmation
        unset($_SESSION['cart']);
        $_SESSION['order_id'] = $orderId;
        header('Location: order_confirmation.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Error processing your order: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Monora</title>
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* [All your existing CSS remains exactly the same] */
        
        /* Only adding error message styling - matches your design */
        .error-message {
            color: #d32f2f;
            background-color: #fde8e8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            text-align: center;
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
            <a href="../api/auth/signout.php"><i class="fas fa-sign-out-alt"></i></a>
            <a href="../views/account.php"><i class="fas fa-user"></i></a>
        </nav>
    </div>
</header>

<main class="checkout-container">
    <div class="checkout-main">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <p>Complete your purchase with Monora</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <div class="checkout-steps">
            <div class="step active">
                <div class="step-number">1</div>
                <div class="step-label">Shipping</div>
            </div>
            <div class="step inactive">
                <div class="step-number">2</div>
                <div class="step-label">Payment</div>
            </div>
            <div class="step inactive">
                <div class="step-number">3</div>
                <div class="step-label">Review</div>
            </div>
        </div>
        
        <form id="checkout-form" action="checkout.php" method="POST">
            <!-- Shipping Address Section -->
            <div class="checkout-section">
                <div class="section-header">
                    <h2 class="section-title">Shipping Address</h2>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="first_name" 
                               value="<?= htmlspecialchars($user['first_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="last_name" 
                               value="<?= htmlspecialchars($user['last_name']) ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" class="form-control" name="street_address" 
                           placeholder="Street address" required>
                </div>
                
                <div class="form-group">
                    <input type="text" class="form-control" name="apt_suite" 
                           placeholder="Apartment, suite, etc. (optional)">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" required>
                    </div>
                    <div class="form-group">
                        <label>State/Province</label>
                        <input type="text" class="form-control" name="state" required>
                    </div>
                    <div class="form-group">
                        <label>ZIP/Postal Code</label>
                        <input type="text" class="form-control" name="zip_code" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Country</label>
                    <select class="form-control" name="country" required>
                        <option value="United States">United States</option>
                        <option value="Canada">Canada</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="Australia">Australia</option>
                        <option value="Germany">Germany</option>
                    </select>
                </div>
            </div>
            
            <!-- Shipping Method Section -->
            <div class="checkout-section">
                <div class="section-header">
                    <h2 class="section-title">Shipping Method</h2>
                </div>
                
                <div class="address-card selected">
                    <div class="address-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="address-details">
                        <h4>Standard Shipping</h4>
                        <p>Delivery in 3-5 business days · Free</p>
                    </div>
                </div>
            </div>
            
            <!-- Payment Method Section -->
            <div class="checkout-section">
                <div class="section-header">
                    <h2 class="section-title">Payment Method</h2>
                </div>
                
                <div class="payment-methods">
                    <label class="payment-card selected">
                        <input type="radio" name="payment_method" value="Cash on Delivery" checked style="display: none;">
                        <div class="payment-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="payment-details">
                            <h4>Cash on Delivery</h4>
                            <p>Pay when you receive your order</p>
                        </div>
                    </label>
                    
                    <label class="payment-card">
                        <input type="radio" name="payment_method" value="Credit Card" style="display: none;">
                        <div class="payment-icon">
                            <i class="far fa-credit-card"></i>
                        </div>
                        <div class="payment-details">
                            <h4>Credit Card</h4>
                            <p>Pay with Visa, Mastercard, or Amex</p>
                        </div>
                    </label>
                    
                    <label class="payment-card">
                        <input type="radio" name="payment_method" value="PayPal" style="display: none;">
                        <div class="payment-icon">
                            <i class="fab fa-paypal"></i>
                        </div>
                        <div class="payment-details">
                            <h4>PayPal</h4>
                            <p>Pay with your PayPal account</p>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Billing Address Section -->
            <div class="checkout-section">
                <div class="section-header">
                    <h2 class="section-title">Billing Address</h2>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="same-as-shipping" checked> Same as shipping address
                    </label>
                </div>
                
                <div id="billing-address-form" style="display: none;">
                    <!-- Billing address fields would go here -->
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="order-summary">
                <h3 class="order-title">Order Summary</h3>
                
                <div class="order-items">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="order-item">
                            <img src="<?= htmlspecialchars($item['details']['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($item['details']['name']) ?>" 
                                 class="order-item-image">
                            <div class="order-item-details">
                                <h4 class="order-item-name"><?= htmlspecialchars($item['details']['name']) ?></h4>
                                <p class="order-item-price">$<?= number_format($item['details']['price'], 2) ?> × <?= $item['quantity'] ?></p>
                            </div>
                            <div class="order-item-total">$<?= number_format($item['details']['price'] * $item['quantity'], 2) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-summary-row">
                    <span>Subtotal</span>
                    <span>$<?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="order-summary-row">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                <div class="order-summary-row">
                    <span>Tax</span>
                    <span>$<?= number_format($tax, 2) ?></span>
                </div>
                <div class="order-summary-row total">
                    <span>Total</span>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>
                
                <button type="submit" name="place_order" class="place-order-btn">Place Order</button>
            </div>
        </form>
    </div>
</main>

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
    // Toggle billing address form
    document.getElementById('same-as-shipping').addEventListener('change', function() {
        const form = document.getElementById('billing-address-form');
        form.style.display = this.checked ? 'none' : 'block';
    });
    
    // Select payment methods
    document.querySelectorAll('.payment-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
</script>
</body>
</html>