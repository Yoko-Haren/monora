<?php
// api/Database.php
// api/Database.php

class Database {
    private $host = 'localhost';
    private $db_name = 'monora';
    private $username = 'root';
    private $password = '';
    private $conn;

    public static function connect() {
        $conn = null;
        try {
            $conn = new PDO(
                "mysql:host=localhost;dbname=monora", 
                'root', 
                '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch(PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }

        return $conn;
    }
}
