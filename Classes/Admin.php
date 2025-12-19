<?php
session_start();

// Only allow admins
if (!isset($_SESSION['logged_in']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../handlers/login.php');
    exit;
}


// Load DB & User classes
require_once(__DIR__ . '/../handlers/Connection.php');

class AdminUsers extends Dbh {
    public function getAllUsers() {
        $conn = $this->connect();
        $stmt = $conn->prepare("SELECT id, email, created_at FROM users ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllReservations() {
        $conn = $this->connect();
        // Simulate reservations from your session logic — but better: store in DB later
        // For now, fetch from a `reservations` table (if exists), else return empty.
        // Since your current system uses session only, we'll simulate a DB-based future-ready version.
        $stmt = $conn->prepare("
            SELECT 
                r.id,
                r.name,
                r.email,
                r.contact,
                r.date,
                r.time,
                r.guests,
                r.table_type,
                r.created_at,
                u.email AS user_email
            FROM reservations r
            LEFT JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
        ");
        if (!$stmt) {
            return []; // Fallback if table doesn’t exist yet
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteReservation($id) {
        $conn = $this->connect();
        $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function deleteUser($id) {
        $conn = $this->connect();
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}

$admin = new AdminUsers();
$users = $admin->getAllUsers();
$reservations = $admin->getAllReservations();
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Savory Spot</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .navbar { position: sticky; top: 0; z-index: 50; }
        .stat-value { @apply text-3xl font-bold; }
        .stat-desc { @apply text-sm text-gray-400; }
    </style>
</head>
<body class="bg-neutral text-white">

<!-- Navbar -->
<div class="navbar bg-neutral shadow-lg">
    <div class="navbar-start">
        <a href="home.php" class="btn btn-ghost text-2xl font-serif font-bold text-warning">Savory Spot</a>
    </div>
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
            <li><a href="home.php#home" class="text-warning">Home</a></li>
            <li><a href="home.php#dining" class="text-warning">Dining Areas</a></li>
            <li><a href="reservations.php" class="text-warning">Reservations</a></li>
            <li><a href="admin.php" class="text-warning font-bold">Admin</a></li>
        </ul>
    </div>
    <div class="navbar-end">
        <a href="../handlers/logout.php" class="btn btn-warning btn-sm font-semibold">Logout</a>
    </div>
</div>

<!-- Hero Banner -->
<section class="relative">
    <div class="carousel w-full h-[30vh] md:h-[40vh]">
        <div class="carousel-item w-full">
            <img src="../assets/bg.jpg" class="w-full h-full object-cover brightness-50" alt="Admin View" />
        </div>
    </div>
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-5xl font-serif font-light">Admin Dashboard</h1>
            <p class="mt-2 text-gray-300">Manage users, reservations, and system data</p>
        </div>
    </div>
</section>

<!-- Stats Overview -->
<div class="px-4 py-8 max-w-6xl mx-auto">
    <div class="stats stats-vertical md:stats-horizontal shadow">
        <div class="stat bg-base-200 p-6 rounded-xl">
            <div class="stat-title text-gray-400">Total Users</div>
            <div class="stat-value"><?= count($users) ?></div>
            <div class="stat-desc">Active accounts</div>
        </div>
        <div class="stat bg-base-200 p-6 rounded-xl">
            <div class="stat-title text-gray-400">Total Reservations</div>
            <div class="stat-value"><?= count($reservations) ?></div>
            <div class="stat-desc">All time bookings</div>
        </div>
        <div class="stat bg-base-200 p-6 rounded-xl">
            <div class="stat-title text-gray-400">Avg Guests/Booking</div>
            <div class="stat-value">
                <?php
                $totalGuests = array_sum(array_column($reservations, 'guests'));
                $avg = count($reservations) ? round($totalGuests / count($reservations), 1) : 0;
                echo $avg;
                ?>
            </div>
            <div class="stat-desc">Average party size</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs my-8">
        <a class="tab tab-lifted tab-active" onclick="showTab('reservations')">Reservations</a>
        <a class="tab tab-lifted" onclick="showTab('users')">Users</a>
    </div>

    <!-- Reservations Table -->
    <div id="tab-reservations" class="tab-content">
        <?php if (!empty($reservations)): ?>
            <div class="overflow-x-auto rounded-xl border border-gray-700">
                <table class="table table-zebra w-full bg-black">
                    <thead class="text-warning">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Table / Guests</th>
                            <th>Date & Time</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $i => $r): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($r['name'] ?? '—') ?></td>
                                <td class="text-sm"><?= htmlspecialchars($r['email'] ?? $r['user_email'] ?? '—') ?></td>
                                <td>
                                    <span class="badge badge-warning badge-outline"><?= htmlspecialchars($r['table_type']) ?></span>
                                    <br><small class="text-gray-400"><?= $r['guests'] ?> guests</small>
                                </td>
                                <td><?= htmlspecialchars($r['date']) ?> <br><small><?= htmlspecialchars($r['time']) ?></small></td>
                                <td class="text-sm"><?= htmlspecialchars($r['contact'] ?? '—') ?></td>
                                <td>
                                    <form method="POST" action="" class="inline" onsubmit="return confirm('Delete this reservation?')">
                                        <input type="hidden" name="action" value="delete_reservation">
                                        <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                                        <button type="submit" class="btn btn-xs btn-error">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-12 text-gray-400">
                <div class="text-4xl mb-4">📭</div>
                <p>No reservations found.</p>
                <p class="text-sm mt-2">Reservations will appear here once saved to the database.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Users Table -->
    <div id="tab-users" class="tab-content hidden">
        <?php if (!empty($users)): ?>
            <div class="overflow-x-auto rounded-xl border border-gray-700">
                <table class="table table-zebra w-full bg-black">
                    <thead class="text-warning">
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $i => $u): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td class="text-sm"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                                <td>
                                    <?php if ($_SESSION['user_id'] != $u['id']): ?>
                                    <form method="POST" action="" class="inline" onsubmit="return confirm('Delete this user and all their data?')">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn btn-xs btn-error">Delete</button>
                                    </form>
                                    <?php else: ?>
                                    <span class="text-gray-500 italic">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-12 text-gray-400">
                <p>No users found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer id="contact" class="footer p-10 bg-neutral text-neutral-content grid grid-cols-1 md:grid-cols-4 gap-8 border-t border-warning">
    <nav>
        <h6 class="footer-title text-warning font-serif">Admin Panel</h6>
        <a class="link link-hover">View Reports</a>
        <a class="link link-hover">Export Data</a>
        <a class="link link-hover">System Logs</a>
    </nav>
    <nav>
        <h6 class="footer-title text-warning font-serif">Restaurant</h6>
        <a class="link link-hover">Menu Editor</a>
        <a class="link link-hover">Gallery Manager</a>
        <a class="link link-hover">Staff Roster</a>
    </nav>
    <nav>
        <h6 class="footer-title text-warning font-serif">Security</h6>
        <a class="link link-hover">Password Policy</a>
        <a class="link link-hover">2FA Settings</a>
        <a class="link link-hover">Session Control</a>
    </nav>
    <nav>
        <h6 class="footer-title text-warning font-serif">Contact</h6>
        <p>Admin Support: admin@savoryspot.com</p>
        <p>Emergency: +63 917 999 8888</p>
    </nav>
</footer>

<div class="footer footer-center p-4 bg-neutral text-neutral-content text-xs opacity-75">
    <aside>
        <p>Copyright © 2025 — Savory Spot Admin System. Internal Use Only.</p>
    </aside>
</div>

<?php
// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete_reservation' && isset($_POST['reservation_id'])) {
        $id = (int)$_POST['reservation_id'];
        if ($admin->deleteReservation($id)) {
            header('Location: admin.php?msg=Reservation+deleted+successfully.');
            exit;
        } else {
            $error = 'Failed to delete reservation.';
        }
    }

    if ($action === 'delete_user' && isset($_POST['user_id'])) {
        $id = (int)$_POST['user_id'];
        if ($id !== $_SESSION['user_id'] && $admin->deleteUser($id)) {
            header('Location: admin.php?msg=User+deleted+successfully.');
            exit;
        } else {
            $error = 'Cannot delete yourself or invalid user.';
        }
    }
}

if (isset($_GET['msg'])) {
    echo '<script>document.addEventListener("DOMContentLoaded", () => {'
        . 'alert("' . htmlspecialchars($_GET['msg']) . '");'
        . '});</script>';
}
?>

<script>
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab').forEach(el => el.classList.remove('tab-active'));
    document.getElementById('tab-' + tabName).classList.remove('hidden');
    event.target.classList.add('tab-active');
}
</script>

</body>
</html>