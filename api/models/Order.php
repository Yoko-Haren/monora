<?php
class Order {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getOrderById($orderId) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getOrderItems($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT oi.*, p.name, p.image_url 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUserOrders($userId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM orders 
            WHERE user_id = ?
            ORDER BY order_date DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        public function createOrder($userId, $total, $shippingAddress, $paymentMethod) {
        $stmt = $this->pdo->prepare("
            INSERT INTO orders (
                user_id, order_date, total_amount, 
                shipping_address, payment_method, status
            ) VALUES (?, NOW(), ?, ?, ?, 'Processing')
        ");
        $stmt->execute([$userId, $total, $shippingAddress, $paymentMethod]);
        return $this->pdo->lastInsertId();
    }
    
    public function addOrderItem($orderId, $productId, $quantity, $price) {
        $stmt = $this->pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$orderId, $productId, $quantity, $price]);
    }




}