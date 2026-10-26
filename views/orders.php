<?php
// views/orders.php
require_once __DIR__ . '/../api/core/config.php';
require_once __DIR__ . '/../api/controllers/OrdersController.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$ordersController = new OrdersController();
$orders = $ordersController->showOrders($_SESSION['user_id']);

// Format the data for display
$formattedOrders = [];
foreach ($orders as $order) {
    $formattedOrders[] = [
        'order_id' => $order['order_id'],
        'order_date' => $order['order_date'],
        'total_amount' => $order['total_amount'],
        'status' => ucfirst(strtolower($order['status']))
    ];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders - Monora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --monora-dark: #1d1d1f;
            --monora-light: #f5f5f7;
            --monora-gray: #86868b;
            --monora-blue: #0066cc;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background-color: var(--monora-light);
            color: var(--monora-dark);
            line-height: 1.5;
        }
        
        .monora-header {
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
            color: var(--monora-dark);
            text-decoration: none;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 22px;
        }
        
        h1 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 24px;
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
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        }
        
        .orders-table th {
            text-align: left;
            padding: 16px 24px;
            background-color: #fafafa;
            font-weight: 600;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .orders-table td {
            padding: 16px 24px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .orders-table tr:last-child td {
            border-bottom: none;
        }
        
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .status-delivered {
            background-color: #e6f7e6;
            color: #1a8828;
        }
        
        .status-shipped {
            background-color: #e6f2ff;
            color: var(--monora-blue);
        }
        
        .status-processing {
            background-color: #fff4e6;
            color: #cc5200;
        }
        
        .view-link {
            color: var(--monora-blue);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
        }
        
        .view-link:hover {
            text-decoration: underline;
            opacity: 0.8;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 0;
            color: var(--monora-gray);
        }
        
        .monora-footer {
            background-color: var(--monora-light);
            color: var(--monora-gray);
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
            text-align: center;
        }
        
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: var(--monora-gray);
            text-decoration: none;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    
    <header class="monora-header">
        <div class="header-container">
        <a href="../views/dashboard.php" class="apple-logo">Monora</a>
            <nav class="main-nav">
                <a href="#">Store</a>
                <a href="#">New Arrivals</a>
                <a href="#">Sales</a>
                <a href="#">Support</a>
            </nav>

            <nav class="utility-nav">
                <a href="../views/bag.php">Bag</a>
                <a href="../api/auth/signout.php">Sign Out</a>
                <a href="../views/account.php">Account</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <h1>Your Orders</h1>
        
        <?php if (!empty($orders)): ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($formattedOrders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_id']) ?></td>
                            <td><?= date('F j, Y', strtotime($order['order_date'])) ?></td>
                            <td>$<?= number_format($order['total_amount'], 2) ?></td>
                            <td>
                                <span class="status status-<?= strtolower($order['status']) ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="../views/product_details.php" class="view-link">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>You haven't placed any orders yet.</p>
                <p>When you do, they'll appear here.</p>
            </div>
        <?php endif; ?>
    </main>

    <footer class="monora-footer">
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
</body>
</html>