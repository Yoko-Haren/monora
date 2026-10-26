<?php
session_start();
require_once __DIR__ . '/../api/core/config.php';
require_once __DIR__ . '/../api/models/User.php';
require_once __DIR__ . '/../api/models/Order.php';
require_once __DIR__ . '/../api/models/Product.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: dashboard.php');
    exit;
}

$userModel = new User($pdo);
$orderModel = new Order($pdo);
$productModel = new ProductModel($pdo);

$user = $userModel->getUserById($_SESSION['user_id']);

// Calculate totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['details']['price'] * $item['quantity'];
}
$tax = $subtotal * 0.08;
$total = $subtotal + $tax;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    try {
        $pdo->beginTransaction();
        
        // Create the order
        $orderId = $orderModel->createOrder(
            $_SESSION['user_id'],
            $total,
            $_POST['street_address'] . "\n" . 
            (!empty($_POST['apt_suite']) ? $_POST['apt_suite'] . "\n" : "") . 
            $_POST['city'] . ", " . $_POST['state'] . " " . $_POST['zip_code'] . "\n" . 
            $_POST['country'],
            $_POST['payment_method']
        );
        
        // Process each cart item
        foreach ($_SESSION['cart'] as $productId => $item) {
            $orderModel->addOrderItem(
                $orderId,
                $productId,
                $item['quantity'],
                $item['details']['price']
            );
            
            $productModel->decrementStock($productId, $item['quantity']);
        }
        
        // Clear the cart
        $pdo->prepare("DELETE FROM bag_items WHERE user_id = ?")
            ->execute([$_SESSION['user_id']]);
        
        $pdo->commit();
        
        unset($_SESSION['cart']);
        $_SESSION['order_id'] = $orderId;
        header('Location: order_confirmation.php');
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error processing your order: " . $e->getMessage();
        error_log("Checkout Error: " . $e->getMessage());
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
        
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 22px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
        }
        
        @media (min-width: 992px) {
            .checkout-container {
                grid-template-columns: 2fr 1fr;
            }
        }
        
        .checkout-header {
            text-align: center;
            margin-bottom: 40px;
            grid-column: 1 / -1;
        }
        
        .checkout-header h1 {
            font-size: 40px;
            font-weight: 600;
            margin: 0 0 10px;
            letter-spacing: -1px;
        }
        
        .checkout-header p {
            font-size: 18px;
            opacity: 0.7;
            margin: 0;
        }
        
        .checkout-steps {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
            gap: 30px;
            grid-column: 1 / -1;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        
        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--text-color);
            color: var(--bg-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .step.active .step-number {
            background-color: var(--text-color);
        }
        
        .step.inactive .step-number {
            background-color: var(--border-color);
        }
        
        .step-label {
            font-size: 14px;
            font-weight: 500;
            opacity: 0.8;
        }
        
        .step::after {
            content: '';
            position: absolute;
            top: 16px;
            left: calc(100% + 10px);
            width: 30px;
            height: 1px;
            background-color: var(--border-color);
        }
        
        .step:last-child::after {
            display: none;
        }
        
        .checkout-section {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        
        .edit-link {
            font-size: 14px;
            opacity: 0.7;
            cursor: pointer;
        }
        
        .edit-link:hover {
            opacity: 1;
        }
        
        .address-card {
            display: flex;
            gap: 20px;
            padding: 20px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 15px;
        }
        
        .address-card.selected {
            border-color: var(--text-color);
            background-color: var(--hover-bg);
        }
        
        .address-icon {
            font-size: 24px;
            opacity: 0.7;
        }
        
        .address-details h4 {
            margin: 0 0 8px;
            font-size: 16px;
        }
        
        .address-details p {
            margin: 0;
            opacity: 0.7;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .payment-card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
        }
        
        .payment-card.selected {
            border-color: var(--text-color);
            background-color: var(--hover-bg);
        }
        
        .payment-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-color);
            border-radius: 8px;
            font-size: 20px;
        }
        
        .payment-details {
            flex: 1;
        }
        
        .payment-details h4 {
            margin: 0 0 4px;
            font-size: 16px;
        }
        
        .payment-details p {
            margin: 0;
            opacity: 0.7;
            font-size: 14px;
        }
        
        .order-summary {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 30px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 100px;
            height: fit-content;
        }
        
        .order-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 25px;
        }
        
        .order-items {
            margin-bottom: 20px;
        }
        
        .order-item {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .order-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .order-item-image {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 8px;
            background-color: var(--bg-color);
            padding: 5px;
            border: 1px solid var(--border-color);
        }
        
        .order-item-details {
            flex: 1;
        }
        
        .order-item-name {
            font-size: 15px;
            margin: 0 0 5px;
        }
        
        .order-item-price {
            opacity: 0.7;
            font-size: 14px;
        }
        
        .order-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        
        .order-summary-row.total {
            font-weight: 600;
            font-size: 18px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }
        
        .place-order-btn {
            width: 100%;
            background-color: var(--text-color);
            color: var(--bg-color);
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-size: 17px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 25px;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .place-order-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .apple-footer {
            background: var(--bg-color);
            padding: 60px 0 40px;
            border-top: 1px solid var(--border-color);
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
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            opacity: 0.8;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            background: var(--bg-color);
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: var(--text-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.05);
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .checkout-header h1 {
                font-size: 32px;
            }
            
            .checkout-steps {
                gap: 15px;
            }
            
            .step::after {
                width: 15px;
                left: calc(100% + 5px);
            }
            
            .checkout-section {
                padding: 20px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    
        .error-message {
            color: #d32f2f;
            background-color: #fde8e8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            text-align: center;
            grid-column: 1 / -1;
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
    
    <div class="checkout-main">
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
                        <option value="">Select a country</option>
                        <?php
                        $countries = [
                            "Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria",
                            "Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia",
                            "Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cabo Verde","Cambodia",
                            "Cameroon","Canada","Central African Republic","Chad","Chile","China","Colombia","Comoros","Congo (Congo-Brazzaville)",
                            "Costa Rica","Croatia","Cuba","Cyprus","Czechia (Czech Republic)","Democratic Republic of the Congo","Denmark","Djibouti",
                            "Dominica","Dominican Republic","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Eswatini (fmr. Swaziland)",
                            "Ethiopia","Fiji","Finland","France","Gabon","Gambia","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea",
                            "Guinea-Bissau","Guyana","Haiti","Holy See","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland",
                            "Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon",
                            "Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Madagascar","Malawi","Malaysia","Maldives","Mali",
                            "Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Montenegro",
                            "Morocco","Mozambique","Myanmar (formerly Burma)","Namibia","Nauru","Nepal","Netherlands","New Zealand","Nicaragua",
                            "Niger","Nigeria","North Korea","North Macedonia","Norway","Oman","Pakistan","Palau","Palestine State","Panama",
                            "Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis",
                            "Saint Lucia","Saint Vincent and the Grenadines","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal",
                            "Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa",
                            "South Korea","South Sudan","Spain","Sri Lanka","Sudan","Suriname","Sweden","Switzerland","Syria","Tajikistan","Tanzania",
                            "Thailand","Timor-Leste","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda",
                            "Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Venezuela","Vietnam",
                            "Yemen","Zambia","Zimbabwe"
                        ];
                        foreach ($countries as $country) {
                            echo '<option value="' . htmlspecialchars($country) . '">' . htmlspecialchars($country) . '</option>';
                        }
                        ?>
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
        </form>
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
        
        <button type="submit" name="place_order" class="place-order-btn" form="checkout-form">Place Order</button>
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