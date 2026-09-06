<?php
$host = 'localhost';
$user = 'root';      // Default XAMPP username
$pass = '';          // Default XAMPP password (leave empty)
$dbname = 'hotel_demo'; // Make sure this matches your database name in phpMyAdmin

// Enable strict error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // This is the $conn variable that index.php is looking for!
    $conn = new mysqli($host, $user, $pass, $dbname);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    error_log($e->getMessage());
    exit('Database connection failed: ' . $e->getMessage());
}
?>