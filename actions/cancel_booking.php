<?php
session_start();
require_once '../config/db_connect.php';

$user_id = $_SESSION['user_id'] ?? 2;
$booking_id = intval($_GET['id'] ?? 0);

if ($booking_id > 0) {
    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $user_id);
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Booking successfully cancelled.";
    }
}

header("Location: ../dashboard.php");
exit;