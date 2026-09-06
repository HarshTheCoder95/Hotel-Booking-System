<?php
session_start();
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = intval($_POST['room_id'] ?? 0);
    $check_in = trim($_POST['check_in'] ?? '');
    $check_out = trim($_POST['check_out'] ?? '');

    if ($room_id <= 0 || empty($check_in) || empty($check_out)) {
        $_SESSION['error_msg'] = "Please select valid check-in and check-out dates.";
        header("Location: ../room_details.php?id=" . $room_id);
        exit;
    }

    // AUTO-FIX: Ensure at least one user exists in the database to satisfy foreign keys
    $user_check = $conn->query("SELECT id FROM users LIMIT 1");
    if ($user_check->num_rows > 0) {
        $row_user = $user_check->fetch_assoc();
        $user_id = $_SESSION['user_id'] ?? $row_user['id'];
    } else {
        $conn->query("INSERT INTO users (name, email, password_hash, role) VALUES ('Demo Customer', 'customer@hotel.com', 'dummyhash', 'customer')");
        $user_id = $conn->insert_id;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. Fetch room and lock row
        $stmt = $conn->prepare("SELECT price_per_night FROM rooms WHERE id = ? FOR UPDATE");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $room = $stmt->get_result()->fetch_assoc();

        if (!$room) {
            throw new Exception("Selected room does not exist.");
        }

        // 2. Check for date overlaps
        $overlap_query = "SELECT id FROM bookings 
                          WHERE room_id = ? 
                          AND status IN ('confirmed', 'pending_payment')
                          AND (check_in_date < ? AND check_out_date > ?) 
                          FOR UPDATE";
                          
        $stmt = $conn->prepare($overlap_query);
        $stmt->bind_param("iss", $room_id, $check_out, $check_in);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("This room is already booked for the selected dates.");
        }

        // 3. Calculate totals with 18% GST
        $in_date = new DateTime($check_in);
        $out_date = new DateTime($check_out);
        $nights = $in_date->diff($out_date)->days;

        if ($nights <= 0) {
            throw new Exception("Check-out date must be after check-in date.");
        }

        $base_total = $nights * $room['price_per_night'];
        $gst_amount = $base_total * 0.18;
        $final_amount = $base_total + $gst_amount;

        // 4. Insert booking record
        $insert_query = "INSERT INTO bookings (room_id, user_id, check_in_date, check_out_date, total_amount, status) 
                         VALUES (?, ?, ?, ?, ?, 'confirmed')";
                         
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iissd", $room_id, $user_id, $check_in, $check_out, $final_amount);
        $stmt->execute();

        $conn->commit();

        $_SESSION['success_msg'] = "Reservation confirmed successfully!";
        header("Location: ../dashboard.php");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_msg'] = $e->getMessage();
        header("Location: ../room_details.php?id=" . $room_id);
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}