<?php
// api/controllers/OrdersController.php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/Product.php';

class OrdersController {
    private $orderModel;
    private $orderItemModel;
    private $productModel;

    public function __construct() {
        global $pdo;
        $this->orderModel = new Order($pdo);
        $this->orderItemModel = new OrderItem($pdo);
        $this->productModel = new ProductModel($pdo); // Changed to ProductModel
    }

    public function showOrders($userId) {
        try {
            // Get orders without items first (simpler query)
            $orders = $this->orderModel->getUserOrders($userId);
            
            // If you need items, you can load them in order_details.php instead
            return $orders;
            
        } catch (PDOException $e) {
            error_log("OrdersController Error: " . $e->getMessage());
            return [];
        }
    }
}