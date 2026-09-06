<?php
session_start();
require_once 'config/db_connect.php';

$user_id = $_SESSION['user_id'] ?? 2;
$user_name = $_SESSION['name'] ?? 'Valued Customer';

// Fetch bookings for this user joined with room details
$query = "SELECT b.*, r.room_name, r.image_url 
          FROM bookings b 
          JOIN rooms r ON b.room_id = r.id 
          WHERE b.user_id = ? 
          ORDER BY b.id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-main-layout { display: grid; grid-template-columns: 80px 1fr; height: 100vh; background: #f7f9fc; overflow: hidden; }
        .dash-content { padding: 40px 60px; overflow-y: auto; }
        .booking-card-item { background: white; border-radius: 16px; padding: 20px; display: flex; gap: 25px; align-items: center; margin-bottom: 20px; border: 1px solid #edf2f7; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .dash-room-img { width: 140px; height: 100px; border-radius: 12px; background-size: cover; background-position: center; background-color: #e2e8f0; }
        .badge-status { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; display: inline-block; }
        .status-confirmed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .btn-cancel { background: #fee2e2; color: #ef4444; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; text-decoration: none; font-size: 0.85rem; transition: 0.3s; }
        .btn-cancel:hover { background: #f87171; color: white; }
    </style>
</head>
<body class="app-body">

<div class="dashboard-main-layout">
    <nav class="icon-sidebar">
        <div class="sidebar-top">
            <div class="logo-mark"><i class="fa-solid fa-h"></i></div>
            <ul class="nav-icons">
                <li><a href="index.php"><i class="fa-solid fa-house"></i></a></li>
                <li class="active"><a href="dashboard.php"><i class="fa-regular fa-calendar-check"></i></a></li>
                <li><a href="admin/dashboard.php"><i class="fa-solid fa-shield-halved"></i></a></li>
            </ul>
        </div>
    </nav>

    <main class="dash-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 900; color: #1a1a2e;">Welcome, <?php echo htmlspecialchars($user_name); ?></h1>
                <p style="color: #718096; font-weight: 500;">Manage your upcoming reservations and transaction history.</p>
            </div>
            <a href="index.php" class="btn primary" style="width: auto; padding: 12px 24px;">Explore Rooms</a>
        </div>

        <?php if(isset($_SESSION['success_msg'])): ?>
            <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
                <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
            </div>
        <?php endif; ?>

        <h2 style="font-size: 1.3rem; font-weight: 800; color: #1a1a2e; margin-bottom: 20px;">Your Reservations</h2>

        <div class="bookings-list">
            <?php if ($bookings && $bookings->num_rows > 0): ?>
                <?php while ($row = $bookings->fetch_assoc()): ?>
                    <div class="booking-card-item">
                        <div class="dash-room-img" style="background-image: url('<?php echo htmlspecialchars($row['image_url'] ?? 'https://images.unsplash.com/photo-1542314831-c6a4d1424391?auto=format&fit=crop&w=400&q=80'); ?>');"></div>
                        
                        <div style="flex: 1;">
                            <h3 style="font-size: 1.2rem; font-weight: 900; color: #1a1a2e; margin-bottom: 5px;"><?php echo htmlspecialchars($row['room_name']); ?></h3>
                            <p style="color: #718096; font-size: 0.9rem; font-weight: 600; margin-bottom: 8px;">
                                <i class="fa-regular fa-calendar" style="color: #b57df3;"></i> <?php echo $row['check_in_date']; ?> &rarr; <?php echo $row['check_out_date']; ?>
                            </p>
                            <span class="badge-status status-<?php echo $row['status'] === 'confirmed' ? 'confirmed' : 'cancelled'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </div>

                        <div style="text-align: right;">
                            <div style="font-size: 1.4rem; font-weight: 900; color: #1a1a2e; margin-bottom: 10px;">₹<?php echo number_format($row['total_amount'], 0); ?></div>
                            <?php if($row['status'] === 'confirmed'): ?>
                                <a href="actions/cancel_booking.php?id=<?php echo $row['id']; ?>" class="btn-cancel" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel Booking</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 60px; background: white; border-radius: 16px; border: 1px solid #edf2f7;">
                    <i class="fa-regular fa-calendar-xmark" style="font-size: 3rem; color: #a0aec0; margin-bottom: 15px;"></i>
                    <h3 style="color: #1a1a2e; margin-bottom: 5px;">No bookings found</h3>
                    <p style="color: #718096; margin-bottom: 20px;">You haven't made any reservations yet.</p>
                    <a href="index.php" class="btn primary" style="width: auto; display: inline-block;">Book a Room Now</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>