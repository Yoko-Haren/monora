<?php
// api/models/ProductModel.php
require_once __DIR__ . '/../Database.php';

class ProductModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public static function getAllProducts() {
        $db = new Database();
        $conn = $db->connect();
        
        $query = "SELECT * FROM products";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($productId) {
        $stmt = $this->pdo->prepare("
            SELECT product_id, name, image_url, price 
            FROM products 
            WHERE product_id = ?
        ");
        $stmt->execute([$productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function decrementStock($productId, $quantity) {
        $stmt = $this->pdo->prepare("
            UPDATE products 
            SET stock = stock - ? 
            WHERE product_id = ? AND stock >= ?
        ");
        $stmt->execute([$quantity, $productId, $quantity]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("Insufficient stock for product $productId");
        }
    }
}