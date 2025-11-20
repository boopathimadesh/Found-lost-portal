<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function registerUser($name, $email, $password) {
    global $pdo;
    
    // Check if email ends with @kongu.edu
    if (!preg_match('/@kongu\.edu$/', $email)) {
        return "Only @kongu.edu emails are allowed for registration.";
    }
    
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        return "User with this email already exists.";
    }
    
    // Hash password and create user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$name, $email, $hashedPassword])) {
        return true;
    }
    
    return "Registration failed. Please try again.";
}

function loginUser($email, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        return true;
    }
    
    return "Invalid email or password.";
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function logout() {
    session_destroy();
    header('Location: ../login.php');
    exit;
}
?>