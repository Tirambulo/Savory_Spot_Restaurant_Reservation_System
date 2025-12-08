<?php
session_start();

/* ---------------------------------
    CONFIGURATION DATA
----------------------------------- */
if (!isset($_SESSION['reservations'])) {
    $_SESSION['reservations'] = [];
}

$dining_areas = [
    ['title'=>'The Chef’s Table','capacity'=>4,'description'=>'Culinary artistry up close. Near the open kitchen.','image'=>'../assets/table1.jpg'],
    ['title'=>'Intimate Bistro Tables','capacity'=>2,'description'=>'Cozy seating with romantic ambient lighting.','image'=>'../assets/table2.jpg'],
    ['title'=>'The Grand Salon','capacity'=>12,'description'=>'Private soundproof room for larger celebrations.','image'=>'../assets/table3.jpg'],
    ['title'=>'Al Fresco Terrace','capacity'=>4,'description'=>'Outdoor terrace with a breathtaking view.','image'=>'../assets/table4.jpg'],
    ['title'=>'Main Dining Hall','capacity'=>4,'description'=>'Comfortable and versatile standard seating.','image'=>'../assets/table5.jpg'],
];
/* ---------------------------------
    Handle Form Submissions
----------------------------------- */
$errors = [];
$form_data = [];
$confirmation_message = null;
$modal_open = false;
$editing_index = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // CREATE
    if ($action === 'reserve') {
        $form_data['date'] = $_POST['date'] ?? '';
        $form_data['time'] = $_POST['time'] ?? '';
        $form_data['name'] = $_POST['name'] ?? '';
        $form_data['email'] = $_POST['email'] ?? '';
        $form_data['contact'] = $_POST['contact'] ?? '';
        $form_data['guests'] = isset($_POST['guests']) ? intval($_POST['guests']) : 0;
        $form_data['table_type'] = $_POST['table_type'] ?? '';

        // Validation
        if (!$form_data['date'] || !$form_data['time']) $errors[] = "Date and Time are required.";
        if (!$form_data['name']) $errors[] = "Full Name is required.";
        if (!$form_data['email']) $errors[] = "Email is required.";
        if (!$form_data['contact']) $errors[] = "Contact number is required.";
        if ($form_data['guests'] < 1 || $form_data['guests'] > 12) $errors[] = "Guest count must be 1-12.";
        if (!$form_data['table_type']) $errors[] = "Table type selection is required.";

        $selected_area = array_filter($dining_areas, fn($a) => $a['title'] === $form_data['table_type']);
        $selected_area = reset($selected_area);
        if ($selected_area && $form_data['guests'] > $selected_area['capacity']) {
            $errors[] = "Selected table '{$selected_area['title']}' allows a maximum of {$selected_area['capacity']} guests.";
        }

        if (empty($errors)) {
            $_SESSION['reservations'][] = $form_data;
            header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=Reservation submitted successfully!');
            exit;
        } else {
            $modal_open = true;
        }
    }

    // UPDATE
    if ($action === 'update') {
        $edit_index = isset($_POST['edit_index']) ? intval($_POST['edit_index']) : null;
        if ($edit_index === null || !isset($_SESSION['reservations'][$edit_index])) {
            $errors[] = "Invalid reservation index for update.";
        } else {
            $form_data['date'] = $_POST['date'] ?? '';
            $form_data['time'] = $_POST['time'] ?? '';
            $form_data['name'] = $_POST['name'] ?? '';
            $form_data['email'] = $_POST['email'] ?? '';
            $form_data['contact'] = $_POST['contact'] ?? '';
            $form_data['guests'] = isset($_POST['guests']) ? intval($_POST['guests']) : 0;
            $form_data['table_type'] = $_POST['table_type'] ?? '';

            // Validation
            if (!$form_data['date'] || !$form_data['time']) $errors[] = "Date and Time are required.";
            if (!$form_data['name']) $errors[] = "Full Name is required.";
            if (!$form_data['email']) $errors[] = "Email is required.";
            if (!$form_data['contact']) $errors[] = "Contact number is required.";
            if ($form_data['guests'] < 1 || $form_data['guests'] > 12) $errors[] = "Guest count must be 1-12.";
            if (!$form_data['table_type']) $errors[] = "Table type selection is required.";

            $selected_area = array_filter($dining_areas, fn($a) => $a['title'] === $form_data['table_type']);
            $selected_area = reset($selected_area);
            if ($selected_area && $form_data['guests'] > $selected_area['capacity']) {
                $errors[] = "Selected table '{$selected_area['title']}' allows a maximum of {$selected_area['capacity']} guests.";
            }

            if (empty($errors)) {
                $_SESSION['reservations'][$edit_index] = $form_data;
                header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=Reservation updated successfully!');
                exit;
            } else {
                $modal_open = true;
            }
        }
    }

    // DELETE is now handled by the form in the confirmation modal.
    if ($action === 'delete') {
        $del_index = isset($_POST['del_index']) ? intval($_POST['del_index']) : null;
        if ($del_index === null || !isset($_SESSION['reservations'][$del_index])) {
            $errors[] = "Invalid reservation index for deletion.";
        } else {
            array_splice($_SESSION['reservations'], $del_index, 1);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=Reservation deleted successfully!');
            exit;
        }
    }
}

if (isset($_GET['msg'])) {
    $confirmation_message = htmlspecialchars($_GET['msg']);
}
?>
<!DOCTYPE html>
<html lang="en" autocomplete="off">
<head>
<meta charset="UTF-8">
<title>Savory Spot | Reservations</title>
<link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
.navbar {
    position: sticky;
    top: 0;
    z-index: 50;
    background-color: #1f2937;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* --- Slideshow Banner --- */
.slideshow-container {
    position: relative;
    width: 100%;
    height: 40vh;
    overflow: hidden;
}
@media (min-width: 768px) {
    .slideshow-container {
        height: 60vh;
    }
}
.slideshow-slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    opacity: 0;
    animation: fadeSlideshow 18s infinite;
}
.slideshow-slide:nth-child(1) { animation-delay: 0s; }
.slideshow-slide:nth-child(2) { animation-delay: 6s; }
.slideshow-slide:nth-child(3) { animation-delay: 12s; }

@keyframes fadeSlideshow {
    0%, 100% { opacity: 0; }
    16.666%, 33.333% { opacity: 1; }
    50%, 83.333% { opacity: 0; }
}

.slideshow-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 1;
}

/* Existing Styles (unchanged) */
.seating-card {
    border: 2px solid transparent;
    transition: all 0.2s;
}
.seating-card:hover {
    border-color: #fbbd23;
}
input[type="radio"]:checked + .seating-card {
    border-color: #fbbd23;
    box-shadow: 0 0 10px rgba(251, 189, 35, 0.5);
    background-color: rgba(251, 189, 35, 0.1);
}
.seating-card-image {
    height: 60px;
    width: 100%;
    overflow: hidden;
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
}
.seating-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Compressed reservation card styles */
.compressed-card .card-body { padding: 1rem; }
.compressed-card .card-title { font-size: 1.25rem; }
.compressed-card p { font-size: 0.875rem; }
.compressed-card .text-sm { font-size: 0.75rem; }
.compressed-card figure { height: 80px; }
.compressed-card .space-y-1 div { display: flex; justify-content: space-between; }
.compressed-card .border-y { padding: 0.25rem 0; }
.compressed-card .flex.gap-2 { padding-top: 0.5rem; margin-top: 0.5rem; }
.compressed-card .h-12 { height: 2rem; }
</style>
</head>
<body class="bg-neutral text-white" data-theme="dark">

<div class="navbar shadow-lg">
    <div class="navbar-start">
        <a href="home.php" class="btn btn-ghost text-xl text-warning">← Back to Home</a>
    </div>
    <div class="navbar-center">
        <span class="text-xl text-warning font-bold">Table Reservation</span>
    </div>
    <div class="navbar-end">
    </div>
</div>

<div class="slideshow-container">
    <div class="slideshow-slide" style="background-image: url('../assets/bg.jpg');"></div>
    <div class="slideshow-slide" style="background-image: url('../assets/bg1.jpg');"></div>
    <div class="slideshow-slide" style="background-image: url('../assets/bg2.jpg');"></div>
    <div class="slideshow-overlay"></div>
</div>

<main class="px-4 py-6 flex flex-col items-center min-h-[70vh]">
    <section class="w-full max-w-6xl">
        <h2 class="text-3xl font-serif font-bold text-center text-warning mb-6">My Current Reservations</h2>

        <?php if ($confirmation_message): ?>
            <div class="alert alert-success shadow-lg mb-6 max-w-lg mx-auto">
                <span class="text-lg"><?= $confirmation_message ?></span>
            </div>
        <?php endif; ?>

        <div class="flex justify-center mb-8">
            <button class="btn btn-warning btn-lg shadow-xl" onclick="openCreateModal()">
                Book a New Table Now <span class="ml-2 text-2xl">+</span>
            </button>
        </div>

        <?php if (!empty($_SESSION['reservations'])): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($_SESSION['reservations'] as $i => $res): ?>
                    <?php
                        // Match reservation to dining area for description, image, and fee
                        $area = array_filter($dining_areas, fn($a) => $a['title'] === $res['table_type']);
                        $area = reset($area) ?: [
                            'title' => $res['table_type'],
                            'description' => 'Reserved table',
                            'capacity' => 4,
                            'image' => '../assets/table5.jpg'
                        ];
                    ?>
                    <div class="card w-full bg-black shadow-2xl overflow-hidden transition-all duration-500 hover:shadow-[0_0_40px_rgba(255,193,7,0.3)] border border-gray-700 compressed-card">
                        <figure class="h-20">
                            <img src="<?= htmlspecialchars($area['image']) ?>" alt="<?= htmlspecialchars($area['title']) ?>" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"/>
                        </figure>
                        <div class="card-body p-4">
                            <div class="flex justify-between items-start">
                                <h2 class="card-title text-lg font-serif font-normal text-warning">
                                    <?= htmlspecialchars($area['title']) ?>
                                </h2>
                                <div class="badge badge-outline badge-warning text-xs mt-1">Reserved</div>
                            </div>

                            <p class="text-sm text-gray-400 mb-2 font-light italic h-8">
                                <?= htmlspecialchars($area['description']) ?>
                            </p>

                            <div class="text-xs space-y-1 mb-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Date</span>
                                    <span class="font-medium"><?= htmlspecialchars($res['date']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Time</span>
                                    <span class="font-medium"><?= htmlspecialchars($res['time']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Guests</span>
                                    <span class="font-medium"><?= htmlspecialchars($res['guests']) ?></span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center border-t border-b border-gray-700 py-1">
                                <div class="flex items-center text-sm text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20v-2c0-.523-.328-.962-.75-1.114L15 16m-2-12v2m0 8v2m-6-8h2m-2 4h2M12 21c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8z" />
                                    </svg>
                                    <?= $area['capacity'] ?> Guests
                                </div>
                            </div>

                            <div class="space-y-1 mt-2">
                                <p class="text-xs"><span class="text-gray-400">Name:</span> <span class="font-medium"><?= htmlspecialchars($res['name']) ?></span></p>
                                <p class="text-xs"><span class="text-gray-400">Contact:</span> <span class="font-medium"><?= htmlspecialchars($res['contact']) ?></span></p>
                                <p class="text-xs break-words"><span class="text-gray-400">Email:</span> <span class="font-medium"><?= htmlspecialchars($res['email']) ?></span></p>
                            </div>

                            <div class="flex gap-2 pt-2 mt-2 border-t border-gray-700">
                                <button type="button" class="btn btn-xs btn-ghost flex-1" onclick="openEditModal(<?= $i ?>)">Edit</button>
                                <button type="button" class="btn btn-xs btn-error w-full flex-1" onclick="openDeleteModal(<?= $i ?>)">Delete</button>
                                </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="text-5xl mb-4">🍽️</div>
                <p class="text-xl text-gray-400">You haven't made any reservations yet.</p>
                <p class="text-gray-500 mt-2">Click below to book your first table!</p>
                <button class="btn btn-warning mt-6" onclick="openCreateModal()">Reserve a Table</button>
            </div>
        <?php endif; ?>
    </section>
</main>

<dialog id="new_reservation_modal" class="modal" <?= $modal_open ? 'open' : '' ?>>
<div class="modal-box max-w-4xl bg-base-200 text-white">
    <form method="dialog">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" onclick="closeModal()">✕</button>
    </form>

    <h3 id="modal_title" class="font-bold text-3xl text-warning mb-6 text-center border-b pb-2">New Table Reservation</h3>

    <?php if (!empty($errors)): ?>
        <div role="alert" class="alert alert-error mb-4">
            <ul class="list-disc list-inside text-sm mt-1">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form id="reservation_form" method="POST" autocomplete="off" class="space-y-6">
        <input type="hidden" name="action" id="form_action" value="reserve">
        <input type="hidden" name="edit_index" id="edit_index" value="">

        <h4 class="text-2xl font-serif font-bold border-b border-gray-700 pb-2">Set Date & Party Size</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input autocomplete="off" id="date" name="date" type="date" class="input input-bordered w-full bg-base-300" value="<?= htmlspecialchars($form_data['date'] ?? date('Y-m-d')) ?>" min="<?= date('Y-m-d') ?>" required>
            <input autocomplete="off" id="time" name="time" type="time" class="input input-bordered w-full bg-base-300" value="<?= htmlspecialchars($form_data['time'] ?? '19:00') ?>" required>
            <input autocomplete="off" id="guests" name="guests" type="number" placeholder="Guests (1-12)" value="<?= htmlspecialchars($form_data['guests'] ?? 2) ?>" min="1" max="12" class="input input-bordered w-full bg-base-300" required>
        </div>

        <h4 class="text-2xl font-serif font-bold border-b border-gray-700 pb-2 pt-4">Choose Your Seating</h4>
        <div id="seating_grid" class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <?php foreach ($dining_areas as $area):
                $isChecked = (isset($form_data['table_type']) && $form_data['table_type'] == $area['title']);
            ?>
                <label class="cursor-pointer">
                    <input autocomplete="off" id="table_<?= md5($area['title']) ?>" type="radio" name="table_type" value="<?= htmlspecialchars($area['title']) ?>" class="sr-only seating-radio" <?= $isChecked ? 'checked' : '' ?> required>
                    <div class="seating-card card bg-base-300 shadow-xl p-4 h-full relative">
                        <div class="seating-card-image">
                            <img src="<?= htmlspecialchars($area['image']) ?>" alt="<?= htmlspecialchars($area['title']) ?> seating area">
                        </div>
                        <div class="card-body p-0 pt-2">
                            <h2 class="card-title text-md text-warning mb-1 font-bold"><?= htmlspecialchars($area['title']) ?></h2>
                            <p class="text-xs text-gray-400 mb-2 min-h-10"><?= htmlspecialchars($area['description']) ?></p>
                            <div class="card-actions justify-start">
                                <div class="badge badge-neutral text-xs">MAX <?= $area['capacity'] ?> GUESTS</div>
                            </div>
                        </div>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>

        <h4 class="text-2xl font-serif font-bold border-b border-gray-700 pb-2 pt-4">Contact Details</h4>
        <input autocomplete="off" id="name" type="text" name="name" placeholder="Full Name" class="input input-bordered w-full bg-base-300" value="<?= htmlspecialchars($form_data['name'] ?? '') ?>" required>
        <input autocomplete="off" id="email" type="email" name="email" placeholder="Email Address" class="input input-bordered w-full bg-base-300" value="<?= htmlspecialchars($form_data['email'] ?? '') ?>" required>
        <input autocomplete="off" id="contact" type="text" name="contact" placeholder="Contact Number" class="input input-bordered w-full bg-base-300" value="<?= htmlspecialchars($form_data['contact'] ?? '') ?>" required>

        <div class="modal-action mt-4">
            <button type="submit" class="btn btn-warning w-full text-lg" id="submit_btn">Submit Reservation</button>
        </div>
    </form>
</div>
</dialog>


<dialog id="delete_confirmation_modal" class="modal">
    <div class="modal-box w-11/12 max-w-sm bg-base-200 text-white">
        <h3 class="font-bold text-2xl text-error mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Confirm Deletion
        </h3>
        <p class="py-4 text-gray-300">Are you sure you want to **permanently delete** this reservation?</p>

        <form id="delete_form" method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="del_index" id="delete_index_input" value="">

            <div class="modal-action mt-4">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('delete_confirmation_modal').close()">Cancel</button>
                <button type="submit" class="btn btn-error" onclick="confirmDelete()">Yes, Delete</button>
            </div>
        </form>
    </div>
</dialog>
<script>
function openCreateModal() {
    document.getElementById('modal_title').innerText = 'New Table Reservation';
    document.getElementById('form_action').value = 'reserve';
    document.getElementById('edit_index').value = '';
    document.getElementById('reservation_form').reset();
    document.getElementById('new_reservation_modal').showModal();
}

function openEditModal(index) {
    const reservations = <?= json_encode($_SESSION['reservations']) ?>;
    const res = reservations[index];

    document.getElementById('modal_title').innerText = 'Edit Reservation';
    document.getElementById('form_action').value = 'update';
    document.getElementById('edit_index').value = index;
    document.getElementById('date').value = res.date;
    document.getElementById('time').value = res.time;
    document.getElementById('guests').value = res.guests;
    document.getElementById('name').value = res.name;
    document.getElementById('email').value = res.email;
    document.getElementById('contact').value = res.contact;
    const tableType = res.table_type;
    document.querySelectorAll('input[name="table_type"]').forEach(r => r.checked = r.value === tableType);
    document.getElementById('new_reservation_modal').showModal();
}

function closeModal() {
    document.getElementById('new_reservation_modal').close();
}

// NEW: Delete Modal Functions
function openDeleteModal(index) {
    // Set the index of the reservation to be deleted in the hidden input
    document.getElementById('delete_index_input').value = index;
    // Show the confirmation modal
    document.getElementById('delete_confirmation_modal').showModal();
}

function confirmDelete() {
    // This function runs when the "Yes, Delete" button is clicked.
    // The form submission is handled by the button's type="submit" attribute.
    // We close the modal to prevent visual confusion after submission.
    document.getElementById('delete_confirmation_modal').close();
}
// END NEW

<?php if ($modal_open): ?>
    document.getElementById('new_reservation_modal').showModal();
<?php endif; ?>
</script>

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
        <p>Copyright © 2025 - All rights reserved by Savory Spot Restaurant Booking System</p>
    </aside>
</div>
</body>
</html>