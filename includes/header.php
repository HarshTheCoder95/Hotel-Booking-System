<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Add this line for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo"><a href="index.php">HotelLogo</a></div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/dashboard.php">Admin Panel</a></li>
                <?php else: ?>
                    <li><a href="dashboard.php">My Bookings</a></li>
                <?php endif; ?>
                <li><a href="actions/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['name']); ?>)</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="container">