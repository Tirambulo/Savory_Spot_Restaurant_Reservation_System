<?php
session_start();
require_once('../handlers/database.php'); 

// --- Admin Access Check ---
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../handlers/login.php'); 
    exit;
}

// --- Handle Reservation Actions ---
$success_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $reservation_id = isset($_POST['res_id']) ? intval($_POST['res_id']) : null;
    $action = $_POST['action'];
    $sql = null;

    if ($reservation_id !== null) {
        switch ($action) {
            case 'approve':
                $sql = "UPDATE reservations SET status = 'Approved' WHERE id = ?";
                $success_msg = 'Reservation approved successfully.';
                break;
            case 'cancel':
                $sql = "UPDATE reservations SET status = 'Cancelled' WHERE id = ?";
                $success_msg = 'Reservation cancelled successfully.';
                break;
            case 'delete':
                $sql = "DELETE FROM reservations WHERE id = ?";
                $success_msg = 'Reservation deleted permanently.';
                break;
        }

        if ($sql) {
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $reservation_id);
                $stmt->execute();
                $stmt->close();
            } else {
                $success_msg = 'Error processing action: Database statement failed.';
            }
        }

        header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($success_msg) . '#reservations');
        exit;
    }
}

$confirmation_message = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : null;

// --- Fetch All Reservations ---
$reservations = [];
$result = $conn->query("SELECT * FROM reservations ORDER BY date ASC, time ASC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
}

// --- Dining Areas ---
$dining_areas = [
    'The Chef’s Table' => ['image'=>'../assets/table1.jpg', 'capacity'=>4, 'description'=>'Experience culinary artistry up close.'],
    'Intimate Bistro Tables' => ['image'=>'../assets/table2.jpg', 'capacity'=>2, 'description'=>'Cozy seating with romantic ambient lighting.'],
    'The Grand Salon' => ['image'=>'../assets/table3.jpg', 'capacity'=>12, 'description'=>'Private soundproof room for larger celebrations.'],
    'Al Fresco Terrace' => ['image'=>'../assets/table4.jpg', 'capacity'=>8, 'description'=>'Outdoor terrace with a breathtaking view.'],
    'Main Dining Hall' => ['image'=>'../assets/table5.jpg', 'capacity'=>10, 'description'=>'Standard restaurant seating.'],
    'Standard' => ['image'=>'../assets/table5.jpg', 'capacity'=>4, 'description'=>'Standard restaurant seating.'],
];

include(__DIR__ . '/../includes/header.php'); 
?>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        .status-Approved { background-color: #10b981; color: white; }
        .status-Cancelled { background-color: #ef4444; color: white; }
        .status-Pending { background-color: #f59e0b; color: #1f2937; }

        .admin-stat-card { transition: transform 0.3s, box-shadow 0.3s; border: 1px solid #4b5563; }
        .admin-stat-card:hover { transform: translateY(-5px); box-shadow: 0 0 25px rgba(255, 193, 7, 0.4); }

        .res-card-figure { height: 100px; overflow: hidden; } 
        .res-card-figure img { width: 100%; height: 100%; object-fit: cover; }
        .res-card-body-compressed { padding: 0.75rem; } 
        .res-card-body-compressed .card-title { font-size: 1.25rem; } 
        .action-btn-group button { height: 2rem; min-height: 2rem; font-size: 0.75rem; } 

        body { font-family: 'Poppins', sans-serif; }
        .font-serif { font-family: Georgia, serif; }
    </style>
</head>

<body data-theme="dark" class="bg-base-300 min-h-screen">
    
    <!-- Navbar -->
    <div class="navbar bg-neutral shadow-lg sticky top-0 z-50">
        <div class="navbar-start">
            <a href="admin_home.php" class="btn btn-warning btn-md font-semibold text-black hover:text-white">
                ← Back to Admin Home
            </a>
        </div>

        <!-- Enhanced Reservations Button -->
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1">
                <li>
                    <a href="#reservations" 
                       class="font-medium text-lg text-white bg-gradient-to-r from-yellow-400 via-orange-400 to-red-400 
                              px-6 py-2 rounded-lg shadow-lg hover:shadow-2xl hover:scale-105 transition-transform duration-300">
                       Reservations
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Hero Section -->
    <section class="relative">
        <div class="carousel w-full h-[40vh] md:h-[60vh]">
            <div id="item1" class="carousel-item w-full">
                <img src="../assets/bg.jpg" class="w-full h-full object-cover brightness-75" alt="Fine Dining Room">
            </div>
            <div id="item2" class="carousel-item w-full hidden">
                <img src="../assets/bg1.jpg" class="w-full h-full object-cover brightness-75" alt="Gourmet Meal">
            </div>
            <div id="item3" class="carousel-item w-full hidden">
                <img src="../assets/bg2.jpg" class="w-full h-full object-cover brightness-75" alt="Elegant Table Setting">
            </div>
        </div>

        <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40">
            <div class="text-center text-white p-6 max-w-4xl">
                <h1 class="text-5xl md:text-7xl font-serif font-light mb-4">
                    Reservations Management
                </h1>
                <p class="text-xl md:text-2xl font-extralight italic text-gray-200">
                    Review, approve, cancel, or delete client bookings.
                </p>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const slides = ['item1','item2','item3'];
        let index = 0;
        slides.forEach((id,i)=>{document.getElementById(id).classList.toggle('hidden', i !== index);});
        setInterval(() => {
            index = (index + 1) % slides.length;
            slides.forEach((id,i)=>{document.getElementById(id).classList.toggle('hidden', i !== index);});
        }, 5000);
    });
    </script>
    
    <!-- Reservations Section -->
    <div class="container mx-auto p-4 md:p-8">
        <?php if ($confirmation_message): ?>
            <div class="alert alert-success shadow-xl mb-8 max-w-lg mx-auto bg-green-900/50 border-green-400 text-white">
                <span><?= $confirmation_message ?></span>
            </div>
        <?php endif; ?>
        
        <h2 id="reservations" class="text-4xl font-serif font-light text-center mb-10 mt-6 text-warning">— All Client Reservations —</h2>

        <?php if (empty($reservations)): ?>
            <div class="text-center py-16 bg-neutral/50 rounded-lg mt-10 shadow-xl">
                <div class="text-5xl mb-4 text-warning">😌</div>
                <p class="text-xl text-gray-300 font-semibold">No current reservations found.</p>
                <p class="text-sm text-gray-500 mt-2">Reservations made by clients will appear here for review.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <?php foreach ($reservations as $res): 
                    $status = $res['status'] ?? 'Pending';
                    $res_id = htmlspecialchars($res['id']);
                    $is_approved = ($status === 'Approved');
                    $is_cancelled = ($status === 'Cancelled');
                    $table_type_key = $res['table_type'] ?? 'Standard';
                    $table_details = $dining_areas[$table_type_key] ?? $dining_areas['Standard']; 
                    $badge_class = 'status-' . str_replace(' ', '', $status);
                ?>
                    <div class="card bg-neutral shadow-xl overflow-hidden admin-stat-card">
                        <figure class="res-card-figure">
                            <img src="<?= htmlspecialchars($table_details['image']) ?>" alt="<?= htmlspecialchars($table_type_key) ?>" />
                        </figure>
                        <div class="card-body res-card-body-compressed">
                            <div class="flex justify-between items-start mb-2">
                                <h2 class="card-title text-warning"><?= htmlspecialchars($table_type_key) ?></h2>
                                <div class="badge <?= $badge_class ?> font-bold text-xs p-2"><?= htmlspecialchars($status) ?></div>
                            </div>
                            <p class="text-xs text-gray-400 mb-2 italic leading-relaxed">
                                ID: #<?= $res_id ?> | Guests: <?= htmlspecialchars($res['guests'] ?? 0) ?>
                            </p>
                            <div class="space-y-1 text-xs text-gray-300 border-b border-gray-700 pb-2 mb-2">
                                <div class="flex justify-between"><span class="text-gray-400">Date/Time:</span> <span class="font-medium"><?= htmlspecialchars($res['date'] ?? 'N/A') ?> @ <?= htmlspecialchars($res['time'] ?? 'N/A') ?></span></div>
                                <div class="flex justify-between"><span class="text-gray-400">Capacity:</span> <span class="font-medium"><?= htmlspecialchars($table_details['capacity'] ?? 0) ?></span></div>
                            </div>
                            <div class="space-y-1 text-xs text-gray-400">
                                <p><span class="text-white font-semibold">Client:</span> <?= htmlspecialchars($res['name'] ?? 'N/A') ?></p>
                                <p class="break-words"><span class="text-white font-semibold">Email:</span> <?= htmlspecialchars($res['email'] ?? 'N/A') ?></p>
                                <p><span class="text-white font-semibold">Contact:</span> <?= htmlspecialchars($res['contact'] ?? 'N/A') ?></p>
                            </div>
                            <div class="flex flex-col space-y-2 mt-4 pt-2 border-t border-gray-700 action-btn-group">
                                <div class="flex gap-2">
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="res_id" value="<?= $res_id ?>"> 
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-success btn-sm w-full font-semibold" <?= $is_approved ? 'disabled' : ''; ?>>Approve</button>
                                    </form>
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="res_id" value="<?= $res_id ?>">
                                        <input type="hidden" name="action" value="cancel">
                                        <button type="submit" class="btn btn-error btn-sm w-full font-semibold" <?= $is_cancelled ? 'disabled' : ''; ?>>Cancel</button>
                                    </form>
                                </div>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to PERMANENTLY DELETE this record?');">
                                    <input type="hidden" name="res_id" value="<?= $res_id ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-outline btn-sm w-full btn-error">Delete Record</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer id="contact" class="footer p-10 bg-neutral text-neutral-content grid grid-cols-2 sm:grid-cols-4 md:grid-cols-4 gap-8 border-t border-warning">
    <nav>
        <h6 class="footer-title text-warning font-serif">Restaurant Services</h6>
        <a class="link link-hover">Fine Dining Menu</a>
        <a class="link link-hover">Event Catering</a>
        <a class="link link-hover">Wine List</a>
        <a class="link link-hover">Special Events</a>
    </nav>
    <nav>
        <h6 class="footer-title text-warning font-serif">Savory Spot</h6>
        <a class="link link-hover">Our Chef</a>
        <a class="link link-hover">Gallery</a>
        <a class="link link-hover">Location Map</a>
        <a class="link link-hover">Guest Reviews</a>
    </nav>
    <nav>
        <h6 class="footer-title text-warning font-serif">Legal</h6>
        <a class="link link-hover">Terms of Service</a>
        <a class="link link-hover">Privacy Policy</a>
        <a class="link link-hover">Accessibility Statement</a>
    </nav>
    <nav>
        <h6 class="footer-title text-warning font-serif">Contact</h6>
        <p>Makati City, Metro Manila, PH</p>
        <p>Reservations: +63 917 123 4567</p>
        <p>Email: reserve@savoryspot.com</p>
    </nav>
</footer>

<div class="footer footer-center p-4 bg-neutral text-neutral-content text-xs opacity-75">
    <aside>
        <p>Copyright © 2025 — All rights reserved by Savory Spot Restaurant Booking System</p>
    </aside>
</div>

<?php 
include(__DIR__ . '/../includes/footer.php'); 
?>