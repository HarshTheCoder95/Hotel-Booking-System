<?php 
require_once 'config/db_connect.php';

// 1. Get all filter values from the URL and save their state
$min_price = $_GET['min_price'] ?? 0;
$max_price = $_GET['max_price'] ?? 25000;
$guests = $_GET['guests'] ?? 1;
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';

// 2. Query the database using both Min and Max price constraints
$query = "SELECT * FROM rooms WHERE is_active = 1 AND price_per_night >= ? AND price_per_night <= ? AND capacity >= ? ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("ddi", $min_price, $max_price, $guests);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Discovery - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
    
    <style>
        .home-layout {
            display: grid; grid-template-columns: 80px 280px 1fr; height: 100vh; background: #f7f9fc; overflow: hidden;
        }
        
        /* Sidebar Filters */
        .filter-sidebar { background: #ffffff; padding: 30px 20px; border-right: 1px solid #e2e8f0; overflow-y: auto; }
        .filter-sidebar h3 { font-size: 1.2rem; margin-bottom: 25px; color: #1a1a2e; }
        .filter-group { margin-bottom: 20px; }
        .filter-group label { display: block; font-weight: 800; margin-bottom: 8px; font-size: 0.75rem; color: #4a5568; text-transform: uppercase;}
        .budget-inputs { display: flex; gap: 10px; align-items: center; }
        .budget-inputs input { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; font-weight: 600; color: #1a1a2e;}
        .budget-inputs input:focus { border-color: #b57df3; }
        
        /* Main Content */
        .home-main { padding: 40px 50px; overflow-y: auto; }
        
        /* Fixed Search Card */
        .search-card {
            background: white; border-radius: 24px; padding: 30px; border: 1px solid #edf2f7;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .input-wrapper { display: flex; flex-direction: column; margin-bottom: 15px; width: 100%; }
        .input-wrapper label { font-size: 0.75rem; font-weight: 800; color: #a26bfa; margin-bottom: 8px; text-transform: uppercase; display: flex; align-items: center; gap: 6px;}
        .input-wrapper input, .input-wrapper select { width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; font-weight: 600; outline: none; background: #f8fafc; font-family: inherit;}
        .input-wrapper input:focus, .input-wrapper select:focus { border-color: #b57df3; background: white; }
        
        .vert-hero { background: linear-gradient(145deg, #a26bfa 0%, #8b46f6 100%); border-radius: 24px; padding: 40px 30px; color: white; display: flex; flex-direction: column; justify-content: space-between;}
        .badge-top { background: rgba(255,255,255,0.2); padding: 6px 14px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 20px; display: inline-block;}
        
        /* Premium Room Cards */
        .rooms-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 35px; padding-bottom: 50px; }
        .pr-card { background: white; border-radius: 20px; padding: 20px; display: flex; flex-direction: column; transition: 0.3s; border: 1px solid #edf2f7; text-decoration: none; position: relative;}
        .pr-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(162,107,250,0.15); border-color: #e2d1f9; }
        
        /* Image and Labels */
        .pr-img { width: 100%; height: 220px; border-radius: 16px; background-size: cover; background-position: center; margin-bottom: 20px; background-color: #e2e8f0; }
        .status-label { position: absolute; top: 35px; left: 35px; background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .pr-tag { position: absolute; top: 35px; right: 35px; background: rgba(181, 125, 243, 0.9); backdrop-filter: blur(4px); color: white; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }
        
        .pr-title { font-size: 1.2rem; color: #1a1a2e; font-weight: 900; margin-bottom: 10px;}
    </style>
</head>
<body class="app-body">

<!-- Master Form: Wraps inputs so they all submit together -->
<form id="mainSearchForm" action="index.php" method="GET"></form>

<div class="home-layout">
    
    <!-- Column 1: Icons -->
    <nav class="icon-sidebar">
        <div class="sidebar-top">
            <div class="logo-mark"><i class="fa-solid fa-h"></i></div>
            <ul class="nav-icons">
                <li class="active"><a href="index.php"><i class="fa-solid fa-house"></i></a></li>
                <li><a href="admin/dashboard.php"><i class="fa-solid fa-shield-halved"></i></a></li>
            </ul>
        </div>
    </nav>

    <!-- Column 2: Filter Sidebar -->
    <aside class="filter-sidebar">
        <h3>Hotel Discovery</h3>
        
        <div class="filter-group">
            <label>Starting Budget (₹)</label>
            <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" form="mainSearchForm" class="budget-inputs" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; font-weight: 600;">
        </div>

        <div class="filter-group">
            <label>Max Budget (₹)</label>
            <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" form="mainSearchForm" class="budget-inputs" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; font-weight: 600;">
        </div>
        
        <div class="filter-group">
            <label>Minimum Guests</label>
            <select name="guests" form="mainSearchForm" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; outline: none; font-family: inherit; font-weight: 600;">
                <option value="1" <?php if($guests==1) echo 'selected'; ?>>1 Guest</option>
                <option value="2" <?php if($guests==2) echo 'selected'; ?>>2 Guests</option>
                <option value="3" <?php if($guests==3) echo 'selected'; ?>>3+ Guests</option>
                <option value="4" <?php if($guests==4) echo 'selected'; ?>>4+ Guests</option>
            </select>
        </div>
        
        <button type="submit" form="mainSearchForm" class="btn primary" style="width: 100%; margin-top: 10px;">Apply Filters</button>
    </aside>

    <!-- Column 3: Main Content -->
    <main class="home-main">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <h1 style="font-size: 2.2rem; font-weight: 900; color: #1a1a2e;">HotelLogo</h1>
        </div>

        <div style="display: grid; grid-template-columns: 320px 320px 1fr; gap: 30px; margin-bottom: 50px;">
            
            <div class="vert-hero">
                <div>
                    <span class="badge-top">Luxury Redefined</span>
                    <h2 style="font-size: 2.2rem; font-weight: 900; line-height: 1.1; margin-bottom: 10px;">Experience the Perfect Escape</h2>
                </div>
                <p style="opacity: 0.9;">Discover world-class accommodations.</p>
            </div>
            
            <!-- Search Card -->
            <div class="search-card">
                <div>
                    <div class="input-wrapper">
                        <label><i class="fa-regular fa-calendar"></i> Check-in</label>
                        <input type="text" id="hp_check_in" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>" placeholder="Select Date" form="mainSearchForm">
                    </div>
                    <div class="input-wrapper">
                        <label><i class="fa-regular fa-calendar-check"></i> Check-out</label>
                        <input type="text" id="hp_check_out" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>" placeholder="Select Date" form="mainSearchForm">
                    </div>
                </div>
                <button type="submit" form="mainSearchForm" class="btn primary" style="width: 100%; font-weight: 800;">Check Availability</button>
            </div>
            
            <!-- Amenities -->
            <div style="display: flex; flex-direction: column; justify-content: center; padding-left: 20px;">
                <h2 style="font-size: 2rem; color: #1a1a2e; margin-bottom: 10px; font-weight: 900;">Handpicked For You</h2>
                <p style="color: #718096; margin-bottom: 30px;">Explore our most popular and luxurious accommodations based on your filters.</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 45px; height: 45px; border-radius: 50%; background: #f3ebff; color: #b57df3; display: flex; justify-content: center; align-items: center;"><i class="fa-solid fa-wifi"></i></div>
                        <div style="font-weight: 800; font-size: 0.9rem; color:#1a1a2e;">High-Speed Wi-Fi</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 45px; height: 45px; border-radius: 50%; background: #f3ebff; color: #b57df3; display: flex; justify-content: center; align-items: center;"><i class="fa-solid fa-water-ladder"></i></div>
                        <div style="font-weight: 800; font-size: 0.9rem; color:#1a1a2e;">Infinity Pool</div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Dynamic Room Grid -->
        <div class="rooms-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($room = $result->fetch_assoc()): ?>
                    
                    <?php 
                    // Guarantee an image always loads even if the database is missing one
                    $img = trim($room['image_url'] ?? '');
                    if(empty($img) || $img === 'default.jpg' || $img === 'default_room.jpg') {
                        $fallback_images = [
                            'https://images.unsplash.com/photo-1542314831-c6a4d1424391?auto=format&fit=crop&w=600&q=80',
                            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=600&q=80',
                            'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=600&q=80',
                            'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=600&q=80'
                        ];
                        // Consistently assign the same backup image to the same room based on its ID
                        $img = $fallback_images[$room['id'] % count($fallback_images)];
                    }
                    ?>

                    <a href="room_details.php?id=<?php echo $room['id']; ?>" class="pr-card">
                        
                        <div class="status-label">Available</div>
                        <?php if($room['price_per_night'] > 3000): ?>
                            <div class="pr-tag">Premium</div>
                        <?php endif; ?>
                        
                        <div class="pr-img" style="background-image: url('<?php echo htmlspecialchars($img); ?>');"></div>
                        
                        <div class="pr-title"><?php echo htmlspecialchars($room['room_name']); ?></div>
                        <div style="font-size: 0.85rem; color: #718096; margin-bottom: 20px; font-weight: 600;">
                            <i class="fa-solid fa-user-group" style="color:#b57df3;"></i> Up to <?php echo htmlspecialchars($room['capacity']); ?> Guests
                        </div>
                        
                        <div style="border-top: 1px solid #edf2f7; padding-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 1.4rem; font-weight: 900; color: #1a1a2e;">₹<?php echo number_format($room['price_per_night'], 0); ?> <span style="font-size: 0.8rem; color:#a0aec0;">/ night</span></div>
                            <div style="background: #f3ebff; color: #b57df3; padding: 8px 16px; border-radius: 8px; font-weight: 800; font-size: 0.85rem;">View</div>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Custom Empty State Message -->
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px; background: white; border-radius: 16px; border: 1px solid #edf2f7;">
                    <i class="fa-solid fa-door-closed" style="font-size: 3rem; color: #a0aec0; margin-bottom: 15px;"></i>
                    <h3 style="color: #1a1a2e; margin-bottom: 5px; font-size: 1.5rem;">Sorry, there is no room available right now.</h3>
                    <p style="color: #718096;">Try adjusting your starting budget, max budget, or guest count to find available options.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#hp_check_in", { minDate: "today", dateFormat: "Y-m-d" });
    flatpickr("#hp_check_out", { minDate: new Date().fp_incr(1), dateFormat: "Y-m-d" });
</script>

</body>
</html>