<?php
session_start();
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'customer')");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        try {
            $stmt->execute();
            $_SESSION['success_msg'] = "Registration successful! Please login.";
            header("Location: ../login.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['error_msg'] = "Email already exists!";
            header("Location: ../register.php");
            exit;
        }
    } 
    
    elseif ($action === 'login') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, name, password_hash, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../dashboard.php");
            }
            exit;
        } else {
            $_SESSION['error_msg'] = "Invalid email or password.";
            header("Location: ../login.php");
            exit;
        }
    }
}