<?php
session_start();
require_once '../api/core/config.php';
require_once '../api/models/User.php';
require_once '../api/models/Order.php';
require_once '../api/models/Product.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to bag
if (isset($_GET['add'])) {
    $productId = (int)$_GET['add'];
    $quantity = 1;
    $addedAt = date('Y-m-d H:i:s');

    $checkStmt = $pdo->prepare("SELECT id FROM bag_items WHERE user_id = ? AND product_id = ?");
    $checkStmt->execute([$userId, $productId]);

    if ($checkStmt->rowCount() === 0) {
        $stmt = $pdo->prepare("INSERT INTO bag_items (user_id, product_id, quantity, added_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $productId, $quantity, $addedAt]);
    } else {
        $updateStmt = $pdo->prepare("UPDATE bag_items SET quantity = quantity + 1, added_at = ? WHERE user_id = ? AND product_id = ?");
        $updateStmt->execute([$addedAt, $userId, $productId]);
    }

    header('Location: bag.php');
    exit;
}

// Remove item
if (isset($_GET['remove'])) {
    $productId = (int)$_GET['remove'];

    $stmt = $pdo->prepare("DELETE FROM bag_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);

    header('Location: bag.php');
    exit;
}

// Update quantities
if (isset($_POST['update_quantity']) && isset($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $productId => $quantity) {
        $productId = (int)$productId;
        $quantity = (int)$quantity;

        if ($quantity <= 0) {
            $stmt = $pdo->prepare("DELETE FROM bag_items WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
        } else {
            $stmt = $pdo->prepare("UPDATE bag_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$quantity, $userId, $productId]);
        }
    }
    header('Location: bag.php');
    exit;
}

// Proceed to checkout
if (isset($_POST['checkout'])) {
    header('Location: checkout.php');
    exit;
}

// Fetch all bag items from DB - CORRECTED JOIN
$stmt = $pdo->prepare("
    SELECT b.product_id, b.quantity, p.name, p.price, p.image_url
    FROM bag_items b
    JOIN products p ON b.product_id = p.product_id
    WHERE b.user_id = ?
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Also fetch recommended products - CORRECTED COLUMN NAME
$productStmt = $pdo->prepare("SELECT product_id, name, price, image_url FROM products ORDER BY RAND() LIMIT 5");
$productStmt->execute();
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

// Sync session cart with database
$_SESSION['cart'] = [];
foreach ($cartItems as $item) {
    $_SESSION['cart'][$item['product_id']] = [
        'details' => [
            'name' => $item['name'],
            'price' => $item['price'],
            'image_url' => $item['image_url']
        ],
        'quantity' => $item['quantity']
    ];
}

// Calculate subtotal, tax, total
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['details']['price'] * $item['quantity'];
}
$tax = $subtotal * 0.08;
$total = $subtotal + $tax;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Bag - Monora</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #fafafa;
            --text-color: #1a1a1a;
            --border-color: #e0e0e0;
            --card-bg: #ffffff;
            --hover-bg: #f0f0f0;
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.03);
            --shadow-md: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 22px;
            flex: 1;
        }
        
        .bag-title {
            text-align: center;
            padding: 40px 0 20px;
            font-size: 40px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        
        .cart-container {
            display: flex;
            flex-direction: column;
            gap: 30px;
            margin-bottom: 60px;
        }
        
        @media (min-width: 768px) {
            .cart-container {
                flex-direction: row;
                align-items: flex-start;
            }
        }
        
        .cart-items {
            flex: 2;
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }
        
        .cart-summary {
            flex: 1;
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            height: fit-content;
            position: sticky;
            top: 100px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }
        
        .cart-item {
            display: flex;
            gap: 20px;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            background-color: var(--hover-bg);
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item-image {
            width: 120px;
            height: 120px;
            object-fit: contain;
            border-radius: 8px;
            background-color: var(--bg-color);
            padding: 10px;
            border: 1px solid var(--border-color);
        }
        
        .cart-item-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .cart-item-name {
            font-size: 19px;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .cart-item-price {
            opacity: 0.7;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }
        
        .quantity-control label {
            font-size: 14px;
            opacity: 0.7;
        }
        
        .quantity-input {
            width: 70px;
            padding: 8px 12px;
            text-align: center;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 15px;
            background: var(--bg-color);
            -moz-appearance: textfield;
        }
        
        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        .remove-item {
            opacity: 0.7;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 15px;
            margin-top: 10px;
            padding: 5px 0;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .remove-item:hover {
            opacity: 1;
        }
        
        .summary-title {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .summary-row span:first-child {
            opacity: 0.7;
        }
        
        .summary-total {
            font-weight: 600;
            font-size: 19px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
            margin-top: 10px;
        }
        
        .checkout-btn {
            width: 100%;
            background-color: var(--text-color);
            color: var(--bg-color);
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-size: 17px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.2s;
        }
        
        .checkout-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .empty-cart {
            text-align: center;
            padding: 80px 0;
        }
        
        .empty-cart p {
            font-size: 19px;
            opacity: 0.7;
            margin-bottom: 25px;
        }
        
        .shop-link {
            opacity: 0.9;
            text-decoration: none;
            font-weight: 500;
            font-size: 17px;
            display: inline-block;
            padding: 10px 20px;
            border: 1px solid var(--text-color);
            border-radius: 25px;
            transition: all 0.2s;
        }
        
        .shop-link:hover {
            background-color: var(--hover-bg);
        }
        
        .update-btn {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 20px;
            cursor: pointer;
            margin: 10px 0 0 20px;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .update-btn:hover {
            background-color: var(--hover-bg);
        }
        
        /* Product Row */
        .product-row {
            margin: 60px 0;
            padding-top: 40px;
            border-top: 1px solid var(--border-color);
        }
        
        .product-row h2 {
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 30px;
            text-align: center;
        }
        
        .product-scroller {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding: 10px 0;
            scrollbar-width: none;
        }
        
        .product-scroller::-webkit-scrollbar {
            display: none;
        }
        
        .product-card {
            min-width: 250px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            background-color: var(--card-bg);
        }
        
        .product-card:hover {
            background-color: var(--hover-bg);
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        
        .product-image-container {
            background-color: var(--bg-color);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 180px;
        }
        
        .product-image {
            width: 100%;
            height: auto;
            max-height: 140px;
            object-fit: contain;
        }
        
        .product-info {
            margin-top: 10px;
        }
        
        .product-name {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .product-price {
            opacity: 0.7;
            margin-bottom: 15px;
            font-size: 15px;
        }
        
        .add-to-cart {
            background-color: var(--text-color);
            color: var(--bg-color);
            border: none;
            border-radius: 8px;
            padding: 10px;
            cursor: pointer;
            width: 100%;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .add-to-cart:hover {
            opacity: 0.9;
        }
        
        /* Footer */
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .bag-title {
                font-size: 32px;
                padding: 30px 0 15px;
            }
            
            .cart-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .cart-item-image {
                width: 150px;
                height: 150px;
            }
            
            .quantity-control {
                justify-content: center;
            }
            
            .update-btn {
                margin: 10px auto 0;
                display: block;
            }
            
            .product-card {
                min-width: 200px;
            }
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

<main class="container">
    <h1 class="bag-title">Your Bag</h1>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <p>Your bag is empty.</p>
            <a href="../views/dashboard.php" class="shop-link">Continue Shopping</a>
        </div>
    <?php else: ?>
        <form action="" method="post">
            <div class="cart-container">
                <div class="cart-items">
                    <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
                        <div class="cart-item">
                            <img src="<?= htmlspecialchars($item['details']['image_url']) ?>" alt="<?= htmlspecialchars($item['details']['name']) ?>" class="cart-item-image">
                            <div class="cart-item-details">
                                <h3 class="cart-item-name"><?= htmlspecialchars($item['details']['name']) ?></h3>
                                <div class="cart-item-price">$<?= number_format($item['details']['price'], 2) ?></div>
                                <div class="quantity-control">
                                    <label for="quantity-<?= $productId ?>">Qty:</label>
                                    <input type="number" id="quantity-<?= $productId ?>" name="quantities[<?= $productId ?>]" value="<?= $item['quantity'] ?>" min="1" class="quantity-input">
                                </div>
                                <button type="button" class="remove-item" onclick="window.location.href='?remove=<?= $productId ?>'">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" name="update_quantity" class="update-btn">Update Bag</button>
                </div>
                
                <div class="cart-summary">
                    <h2 class="summary-title">Summary</h2>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Estimated Tax</span>
                        <span>$<?= number_format($tax, 2) ?></span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>
                    <button type="submit" name="checkout" class="checkout-btn">Check Out</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
        
    <!-- Product Recommendations -->
    <div class="product-row" id="products">
        <h2>Upcoming!!!</h2>
        <div class="product-scroller">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <?php if ($product['product_id'] != 1): ?>
                        <div class="product-card">
                            <div class="product-image-container">
                                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                            </div>
                            <div class="product-info">
                                <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                                <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                                <button class="add-to-cart" disabled>Coming soon...</button>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (count(array_filter($products, fn($p) => $p['product_id'] != 1)) === 0): ?>
                    <p>No products available at the moment.</p>
                <?php endif; ?>
            <?php else: ?>
                <p>No products available at the moment.</p>
            <?php endif; ?>
        </div>
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
    // Add smooth interactions
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // Simple confirmation for remove action
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this item from your bag?')) {
                window.location.href = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            }
        });
    });
</script>
</body>
</html>