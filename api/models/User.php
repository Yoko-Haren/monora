<?php
// api/User.php

require_once __DIR__ . '/../Database.php';


class User {
    private $db;
    private $table = 'users';

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function emailExists($email) {
        $query = "SELECT user_id FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function usernameExists($username) {
        $query = "SELECT user_id FROM {$this->table} WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function findByUsernameOrEmail($value) {
        $query = "SELECT * FROM {$this->table} WHERE username = :username OR email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
    
        // Bind the parameters correctly
        $stmt->bindParam(':username', $value);
        $stmt->bindParam(':email', $value);
    
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    
        return null;
    }

    public function getUserById($userId) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    
        return null;
    }   

    public function login($usernameOrEmail, $password) {
        // Find user by username or email
        $user = $this->findByUsernameOrEmail($usernameOrEmail);
    
        if ($user) {
            // Verify password hash
            if (password_verify($password, $user['password_hash'])) {
                return $user; // Return user data if credentials are correct
            } else {
                throw new Exception("Invalid credentials.");
            }
        } else {
            throw new Exception("User not found.");
        }
    }

    public function update($id, $firstName, $lastName, $email) {
        $query = "UPDATE {$this->table} SET first_name = :first_name, last_name = :last_name, email = :email WHERE user_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }    
    
    public function create($data) {
        // Validate required fields
        $required = ['first_name', 'last_name', 'email', 'username', 'password', 'country', 'birth_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("The {$field} field is required.");
            }
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        // Check if email or username exists
        if ($this->emailExists($data['email'])) {
            throw new Exception("Email address already registered.");
        }

        if ($this->usernameExists($data['username'])) {
            throw new Exception("Username already taken.");
        }

        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        // Insert user
        $query = "INSERT INTO {$this->table} 
                 (first_name, last_name, email, username, password_hash, country, birth_date) 
                 VALUES (:first_name, :last_name, :email, :username, :password, :country, :birth_date)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':country', $data['country']);
        $stmt->bindParam(':birth_date', $data['birth_date']);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }

        throw new Exception("Registration failed. Please try again.");


    }

    public function updatePassword($userId, $newPasswordHash) {
    $query = "UPDATE users SET password_hash = :password WHERE user_id = :user_id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':password', $newPasswordHash);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    return $stmt->execute();
}
}