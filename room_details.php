<?php 
require_once 'config/db_connect.php';
$room_id = $_GET['id'] ?? 1;

$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
if(!$room) { die("Room not found."); }

// Provide a fallback description if old rooms don't have one
$overview_text = !empty($room['overview']) ? $room['overview'] : "Experience luxury living. These contemporary rooms feature modern designs and high-quality finishes.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($room['room_name']); ?> - Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Override layout to remove the filter sidebar */
        .dashboard-layout {
            display: grid;
            grid-template-columns: 80px 1fr 380px; /* 3 Columns now */
            height: 100vh;
        }
    </style>
</head>
<body class="app-body">

<div id="lightbox"><span id="lightbox-close">&times;</span><img id="lightbox-img" src=""></div>

<div class="dashboard-layout">
    <!-- Column 1: Icon Navigation -->
    <nav class="icon-sidebar">
        <div class="sidebar-top">
            <div class="logo-mark"><i class="fa-solid fa-h"></i></div>
            <ul class="nav-icons">
                <li><a href="index.php"><i class="fa-solid fa-house"></i></a></li>
                <li class="active"><a href="#"><i class="fa-solid fa-bed"></i></a></li>
            </ul>
        </div>
    </nav>

    <!-- Column 2: Main Content -->
    <main class="main-content">
        <div class="premium-gallery">
            <div class="gallery-main zoomable-img" onclick="openLightbox(this)" style="background-image: url('<?php echo $room['image_url']; ?>');"></div>
            <div class="gallery-side">
                <div class="gallery-small zoomable-img" onclick="openLightbox(this)" style="background-image: url('https://images.unsplash.com/photo-1578683010236-d716f9a3f461?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80');"></div>
                <div class="gallery-small zoomable-img" onclick="openLightbox(this)" style="background-image: url('https://images.unsplash.com/photo-1566665797739-1674de7a421a?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80');"></div>
            </div>
        </div>
        
        <div class="room-title-block">
            <h1><?php echo htmlspecialchars($room['room_name']); ?></h1>
            <p><i class="fa-solid fa-location-dot"></i> Premium Suite · Up to <?php echo htmlspecialchars($room['capacity']); ?> Guests</p>
        </div>

        <div class="host-info">
            <div class="host-avatar"></div>
            <div>
                <h4>Hosted by Admin</h4>
                <p>Superhost · Selling more than 2 years</p>
            </div>
        </div>
        
        <div class="room-overview">
            <h2>Overview</h2>
            <!-- Dynamically pulling the Admin's text -->
            <p><?php echo nl2br(htmlspecialchars($overview_text)); ?></p>
        </div>
        
        <div class="amenities-grid">
            <h2>What this place offers</h2>
            <div class="grid-container">
                <div class="grid-item"><i class="fa-solid fa-wifi"></i> Fast Wi-Fi</div>
                <div class="grid-item"><i class="fa-solid fa-water-ladder"></i> Pool</div>
            </div>
        </div>
    </main>

    <!-- Column 3: Booking Widget (GST Math) -->
    <div class="booking-card">
    <!-- Error Alert Banner -->
    <?php if(isset($_SESSION['error_msg'])): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 0.85rem; font-weight: 600;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
        </div>
    <?php endif; ?>

    <div class="price-header">
        <span class="new-price">₹<?php echo number_format($room['price_per_night'], 0); ?></span>
        <span class="night-text">/ night</span>
    </div>
    <!-- Rest of your booking form... -->
            
            <form action="actions/process_booking.php" method="POST" id="bookingForm">
                <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                <div class="premium-date-picker">
                    <div class="date-input-box border-right">
                        <label>CHECK-IN</label>
                        <input type="text" id="check_in" name="check_in" placeholder="Select Date" required>
                    </div>
                    <div class="date-input-box">
                        <label>CHECK-OUT</label>
                        <input type="text" id="check_out" name="check_out" placeholder="Select Date" required>
                    </div>
                </div>
                
                <div id="priceCalculator" class="price-calculator" style="display: none; margin-top: 15px;">
                    <div class="calc-row"><span id="calcNights"></span><span id="calcBase"></span></div>
                    <div class="calc-row"><span>GST (18%)</span><span id="calcTax"></span></div>
                    <div class="calc-row calc-final"><span>Total (INR)</span><span id="finalTotal"></span></div>
                </div>

                <button type="submit" class="btn primary full-btn" style="margin-top: 20px;">Book Now</button>
            </form>
        </div>
    </aside>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#check_in", { minDate: "today", dateFormat: "Y-m-d", onChange: calculatePrice });
    flatpickr("#check_out", { minDate: new Date().fp_incr(1), dateFormat: "Y-m-d", onChange: calculatePrice });

    const checkInInput = document.getElementById('check_in'), checkOutInput = document.getElementById('check_out');
    const calculatorDiv = document.getElementById('priceCalculator');
    const pricePerNight = <?php echo $room['price_per_night']; ?>;

    function calculatePrice() {
        if(checkInInput.value && checkOutInput.value) {
            const checkIn = new Date(checkInInput.value), checkOut = new Date(checkOutInput.value);
            if (checkOut > checkIn) {
                const days = (checkOut - checkIn) / (1000 * 3600 * 24);
                const base = days * pricePerNight, gst = base * 0.18, total = base + gst;
                
                document.getElementById('calcNights').innerText = `₹${pricePerNight.toLocaleString('en-IN')} x ${days} night(s)`;
                document.getElementById('calcBase').innerText = `₹${base.toLocaleString('en-IN')}`;
                document.getElementById('calcTax').innerText = `₹${gst.toLocaleString('en-IN')}`;
                document.getElementById('finalTotal').innerText = `₹${total.toLocaleString('en-IN')}`;
                calculatorDiv.style.display = "block";
                return;
            }
        }
        calculatorDiv.style.display = "none";
    }

    const lightbox = document.getElementById('lightbox'), lightboxImg = document.getElementById('lightbox-img');
    function openLightbox(el) { lightboxImg.src = el.style.backgroundImage.replace(/(url\(|\)|"|')/g, ''); lightbox.style.display = "flex"; }
    document.getElementById('lightbox-close').onclick = () => lightbox.style.display = "none";
</script>
</body>
</html>