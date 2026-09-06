<?php
require_once '../config/db_connect.php';

// AUTO-FIX: Ensure image_url and overview columns exist
$conn->query("ALTER TABLE rooms ADD COLUMN IF NOT EXISTS image_url VARCHAR(500) DEFAULT 'default.jpg'");
$conn->query("ALTER TABLE rooms ADD COLUMN IF NOT EXISTS overview TEXT");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_name = $_POST['room_name'];
    $price = $_POST['price'];
    $capacity = $_POST['capacity'];
    $overview = $_POST['overview'];
    
    $random_images = [
        'https://images.unsplash.com/photo-1542314831-c6a4d1424391?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'
    ];
    $image_url = $random_images[array_rand($random_images)];

    $stmt = $conn->prepare("INSERT INTO rooms (room_name, price_per_night, capacity, image_url, overview) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdiss", $room_name, $price, $capacity, $image_url, $overview);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Room</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Base Admin CSS (same as before) */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; background: #f4f7fe; min-height: 100vh; color: #1a1a2e; }
        .sidebar { width: 260px; background: #11111d; color: #fff; padding: 30px 20px; }
        .sidebar h2 { font-size: 1.5rem; margin-bottom: 40px; }
        .sidebar h2 i { color: #b57df3; }
        .nav-menu { list-style: none; }
        .nav-menu a { display: block; padding: 12px 15px; color: #a0aec0; text-decoration: none; border-radius: 8px; font-weight: 600; margin-bottom: 15px;}
        .nav-menu a.active { background: rgba(181, 125, 243, 0.1); color: #b57df3; border-left: 4px solid #b57df3; }
        .content { flex: 1; padding: 40px 50px; }
        .form-container { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); max-width: 800px; }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568; font-size: 0.9rem; text-transform: uppercase;}
        .form-group input, .form-group textarea { width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 1rem; outline: none; font-family: inherit; }
        .form-group input:focus, .form-group textarea:focus { border-color: #b57df3; }
        .btn-submit { background: #b57df3; color: white; border: none; padding: 15px 30px; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; width: 100%; transition: 0.3s;}
        .btn-submit:hover { background: #9d5ce6; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fa-solid fa-h"></i> Admin</h2>
        <ul class="nav-menu">
            <li><a href="dashboard.php" class="active">Manage Rooms</a></li>
            <li><a href="../index.php">Back to Site</a></li>
        </ul>
    </div>
    
    <div class="content">
        <h1 style="margin-bottom: 30px;">Add New Room</h1>
        <div class="form-container">
            <form action="add_room.php" method="POST">
                <div class="form-group">
                    <label>Room Name</label>
                    <input type="text" name="room_name" placeholder="e.g. Skyline Penthouse" required>
                </div>
                
                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Price per Night (₹)</label>
                        <input type="number" name="price" placeholder="2500" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Guest Capacity</label>
                        <input type="number" name="capacity" placeholder="2" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Room Overview / Description</label>
                    <textarea name="overview" rows="4" placeholder="Describe the luxury amenities, view, and vibe of the room..." required></textarea>
                </div>
                
                <button type="submit" class="btn-submit">Save Room Inventory</button>
            </form>
        </div>
    </div>
</body>
</html>