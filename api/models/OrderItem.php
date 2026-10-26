<?php
class OrderItem {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getOrderItems($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM order_items 
            WHERE order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>