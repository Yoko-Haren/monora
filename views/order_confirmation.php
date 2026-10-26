<?php
session_start();
require_once '../api/core/config.php';
require_once '../api/models/Order.php';
require_once '../api/models/OrderItem.php';
require_once '../api/models/Product.php';

if (!isset($_SESSION['order_id'])) {
    header('Location: dashboard.php');
    exit;
}

$orderModel = new Order($pdo);
$orderItemModel = new OrderItem($pdo);
$productModel = new ProductModel($pdo);

$order = $orderModel->getOrderById($_SESSION['order_id']);
$orderItems = $orderItemModel->getOrderItems($_SESSION['order_id']);

// Enhance order items with product details
foreach ($orderItems as &$item) {
    $product = $productModel->getProductById($item['product_id']);
    $item['product_name'] = $product['name'];
    $item['image_url'] = $product['image_url'];
}

unset($_SESSION['order_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Monora</title>
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
            --success-color: #34c759;
            --primary-color: #0071e3;
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
        
        .confirmation-container {
            max-width: 800px;
            margin: 40px auto;
            background: var(--card-bg);
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }
        
        .confirmation-header {
            background-color: var(--success-color);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .confirmation-icon {
            width: 60px;
            height: 60px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--success-color);
            font-size: 24px;
        }
        
        .confirmation-header h1 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .confirmation-header p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .confirmation-body {
            padding: 30px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .order-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .detail-group h3 {
            font-size: 14px;
            opacity: 0.7;
            margin-bottom: 5px;
        }
        
        .detail-group p {
            font-size: 16px;
            font-weight: 500;
        }
        
        .order-items {
            margin-top: 30px;
        }
        
        .order-item {
            display: flex;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item-image {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 8px;
            background-color: var(--bg-color);
            padding: 10px;
            border: 1px solid var(--border-color);
        }
        
        .order-item-details {
            flex: 1;
        }
        
        .order-item-name {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .order-item-price {
            opacity: 0.7;
            margin-bottom: 5px;
            font-size: 15px;
        }
        
        .order-item-quantity {
            opacity: 0.7;
            font-size: 14px;
        }
        
        .order-summary {
            background-color: var(--bg-color);
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        
        .summary-row.total {
            font-weight: 600;
            font-size: 18px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border-color);
        }
        
        .confirmation-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        
        .confirmation-btn {
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
        }
        
        .btn-outline {
            border: 1px solid var(--text-color);
            color: var(--text-color);
        }
        
        .btn-outline:hover {
            background-color: var(--hover-bg);
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
        
        @media (max-width: 768px) {
            .order-details-grid {
                grid-template-columns: 1fr;
            }
            
            .confirmation-actions {
                flex-direction: column;
            }
            
            .confirmation-btn {
                width: 100%;
                text-align: center;
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

<main>
    <div class="confirmation-container">
        <div class="confirmation-header">
            <div class="confirmation-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1>Thank You for Your Order!</h1>
            <p>Your order #<?= htmlspecialchars($order['order_id']) ?> has been placed successfully.</p>
        </div>
        
        <div class="confirmation-body">
            <h2 class="section-title">Order Summary</h2>
            
            <div class="order-details-grid">
                <div class="detail-group">
                    <h3>Order Number</h3>
                    <p>#<?= htmlspecialchars($order['order_id']) ?></p>
                </div>
                <div class="detail-group">
                    <h3>Date</h3>
                    <p><?= date('F j, Y', strtotime($order['order_date'])) ?></p>
                </div>
                <div class="detail-group">
                    <h3>Payment Method</h3>
                    <p><?= htmlspecialchars(ucfirst($order['payment_method'])) ?></p>
                </div>
                <div class="detail-group">
                    <h3>Order Status</h3>
                    <p>Processing</p>
                </div>
            </div>
            
            <h3 class="section-title">Order Items</h3>
            <div class="order-items">
                <?php foreach ($orderItems as $item): ?>
                <div class="order-item">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="order-item-image">
                    <div class="order-item-details">
                        <h4 class="order-item-name"><?= htmlspecialchars($item['product_name']) ?></h4>
                        <p class="order-item-price">$<?= number_format($item['price'], 2) ?></p>
                        <p class="order-item-quantity">Quantity: <?= $item['quantity'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="order-summary">
                <div class="summary-row total">
                    <span>Total</span>
                    <span>$<?= number_format($order['total_amount'], 2) ?></span>
                </div>
            </div>
            
            <div class="confirmation-actions">
                <a href="orders.php" class="confirmation-btn btn-primary">
                    <i class="fas fa-list"></i> View All Orders
                </a>
                <a href="dashboard.php" class="confirmation-btn btn-outline">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
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

</body>
</html>