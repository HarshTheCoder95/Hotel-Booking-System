<?php
require_once '../config/db_connect.php';

// Fetch all bookings joined with user details and room names
$query = "SELECT b.*, u.name as customer_name, u.email as customer_email, r.room_name 
          FROM bookings b 
          JOIN users u ON b.user_id = u.id 
          JOIN rooms r ON b.room_id = r.id 
          ORDER BY b.id DESC";
$bookings = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Bookings</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; background: #f4f7fe; min-height: 100vh; color: #1a1a2e; }
        
        .sidebar { width: 260px; background: #11111d; color: #fff; padding: 30px 20px; }
        .sidebar h2 { font-size: 1.5rem; margin-bottom: 40px; letter-spacing: 1px; }
        .sidebar h2 i { color: #b57df3; }
        .nav-menu { list-style: none; }
        .nav-menu li { margin-bottom: 15px; }
        .nav-menu a { display: block; padding: 12px 15px; color: #a0aec0; text-decoration: none; border-radius: 8px; font-weight: 600; transition: 0.3s; }
        .nav-menu a:hover, .nav-menu a.active { background: rgba(181, 125, 243, 0.1); color: #b57df3; border-left: 4px solid #b57df3; }
        .nav-menu a i { margin-right: 10px; width: 20px; text-align: center; }
        
        .content { flex: 1; padding: 40px 50px; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-flex h1 { font-size: 2rem; color: #11111d; }
        
        .table-container { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #718096; font-size: 0.8rem; text-transform: uppercase; border-bottom: 2px solid #edf2f7; }
        td { padding: 15px; border-bottom: 1px solid #edf2f7; font-weight: 600; color: #2d3748; font-size: 0.95rem; }
        
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }
        .badge-confirmed { background: #dcfce7; color: #166534; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fa-solid fa-h"></i> Admin</h2>
        <ul class="nav-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-bed"></i> Manage Rooms</a></li>
            <li><a href="bookings.php" class="active"><i class="fa-solid fa-book"></i> Bookings</a></li>
            <li><a href="#"><i class="fa-solid fa-users"></i> Customers</a></li>
            <li style="margin-top: 50px;"><a href="../index.php"><i class="fa-solid fa-arrow-left"></i> Back to Site</a></li>
        </ul>
    </div>
    
    <div class="content">
        <div class="header-flex">
            <h1>Customer Reservations</h1>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Room</th>
                        <th>Dates</th>
                        <th>Total (Inc. GST)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bookings && $bookings->num_rows > 0): ?>
                        <?php while ($row = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($row['customer_name']); ?><br>
                                <span style="font-size: 0.8rem; color: #a0aec0; font-weight: 400;"><?php echo htmlspecialchars($row['customer_email']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                            <td style="font-size: 0.85rem; color: #4a5568;">
                                <i class="fa-regular fa-calendar" style="color: #b57df3;"></i> 
                                <?php echo $row['check_in_date']; ?> &rarr; <?php echo $row['check_out_date']; ?>
                            </td>
                            <td style="color: #1a1a2e; font-weight: 900;">₹<?php echo number_format($row['total_amount'], 0); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $row['status'] === 'confirmed' ? 'confirmed' : 'cancelled'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #a0aec0;">No reservations found in the system yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>