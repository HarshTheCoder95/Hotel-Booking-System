<?php
session_start();
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    // 1. Check if the email exists in the users table
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User exists. 
        // In production: Generate a unique token, save it to the DB, and send an email using PHPMailer.
        // For the demo: We simulate a successful email queue.
        $_SESSION['success_msg'] = "If that email exists in our system, a password reset link has been sent.";
    } else {
        // Security Best Practice: Never tell the user if the email does NOT exist. 
        // This prevents hackers from "email-harvesting" your system to see who is registered.
        $_SESSION['success_msg'] = "If that email exists in our system, a password reset link has been sent.";
    }
    
    // Redirect back to login with the success message
    header("Location: ../login.php");
    exit;
}
?>