<?php
require_once '../config/db_connect.php';
$query = "SELECT * FROM rooms ORDER BY id DESC";
$rooms = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Room Inventory</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; background: #f4f7fe; min-height: 100vh; color: #1a1a2e; }
        
        /* Dark Premium Sidebar */
        .sidebar { width: 260px; background: #11111d; color: #fff; padding: 30px 20px; }
        .sidebar h2 { font-size: 1.5rem; margin-bottom: 40px; letter-spacing: 1px; }
        .sidebar h2 i { color: #b57df3; }
        .nav-menu { list-style: none; }
        .nav-menu li { margin-bottom: 15px; }
        .nav-menu a { display: block; padding: 12px 15px; color: #a0aec0; text-decoration: none; border-radius: 8px; font-weight: 600; transition: 0.3s; }
        .nav-menu a:hover, .nav-menu a.active { background: rgba(181, 125, 243, 0.1); color: #b57df3; border-left: 4px solid #b57df3; }
        .nav-menu a i { margin-right: 10px; width: 20px; text-align: center; }
        
        /* Main Content */
        .content { flex: 1; padding: 40px 50px; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-flex h1 { font-size: 2rem; color: #11111d; }
        .btn-add { background: #b57df3; color: white; text-decoration: none; padding: 12px 24px; border-radius: 12px; font-weight: 600; transition: 0.3s; }
        .btn-add:hover { background: #9d5ce6; transform: translateY(-2px); }
        
        /* Premium Table */
        .table-container { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #718096; font-size: 0.8rem; text-transform: uppercase; border-bottom: 2px solid #edf2f7; }
        td { padding: 15px; border-bottom: 1px solid #edf2f7; font-weight: 600; color: #2d3748; }
        .status { background: #dcfce7; color: #166534; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; }
        
        /* Action Buttons */
        .action-btn { display: inline-block; padding: 8px 12px; border-radius: 8px; color: white; text-decoration: none; margin-right: 5px; }
        .edit-btn { background: #3b82f6; }
        .del-btn { background: #ef4444; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fa-solid fa-h"></i> Admin</h2>
        <ul class="nav-menu">
            <li><a href="dashboard.php" class="active"><i class="fa-solid fa-bed"></i> Manage Rooms</a></li>
            <li><a href="bookings.php"><i class="fa-solid fa-book"></i> Bookings</a></li>
            <li><a href="#"><i class="fa-solid fa-users"></i> Customers</a></li>
            <li style="margin-top: 50px;"><a href="../index.php"><i class="fa-solid fa-arrow-left"></i> Back to Site</a></li>
        </ul>
    </div>
    
    <div class="content">
        <div class="header-flex">
            <h1>Room Inventory</h1>
            <a href="add_room.php" class="btn-add">+ Add New Room</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room Name</th>
                        <th>Capacity</th>
                        <th>Price/Night</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $rooms->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                        <td><?php echo $row['capacity']; ?> Guests</td>
                        <td>₹<?php echo number_format($row['price_per_night'], 0); ?></td>
                        <td><span class="status">Active</span></td>
                        <td>
                            <a href="#" class="action-btn edit-btn"><i class="fa-solid fa-pen"></i></a>
                            <a href="#" class="action-btn del-btn"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>