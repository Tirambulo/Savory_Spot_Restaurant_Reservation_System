<?php 
include(__DIR__ . '/../includes/header.php'); 
?>

<div class="navbar bg-neutral shadow-lg sticky top-0 z-50">
    <div class="navbar-start">
        <a class="btn btn-ghost text-2xl font-serif font-bold text-warning hover:bg-transparent">
            Savory Spot — Admin
        </a>
    </div>

    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
            <li><a href="admin_home.php" class="font-medium text-lg text-warning">Dashboard</a></li>
            <li><a href="manage_reservation.php" class="font-medium text-lg text-warning">Reservations</a></li>
            <li><a href="tables" class="font-medium text-lg text-warning"></a></li>
            <li><a href="contact" class="font-medium text-lg text-warning"></a></li>
        </ul>
    </div>

    <div class="navbar-end">
        <a href="../handlers/login.php" class="btn btn-warning btn-sm font-semibold text-black">
            Logout
        </a>
    </div>
</div>

<section class="relative">
    <div class="carousel w-full h-[40vh] md:h-[60vh]">
        <div id="item1" class="carousel-item w-full">
            <img src="../assets/bg.jpg" class="w-full h-full object-cover brightness-75">
        </div>
        <div id="item2" class="carousel-item w-full hidden">
            <img src="../assets/bg1.jpg" class="w-full h-full object-cover brightness-75">
        </div>
        <div id="item3" class="carousel-item w-full hidden">
            <img src="../assets/bg2.jpg" class="w-full h-full object-cover brightness-75">
        </div>
    </div>

    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40">
        <div class="text-center text-white p-6 max-w-4xl">
            <h1 class="text-5xl md:text-7xl font-serif font-light mb-4">
                Admin Dashboard
            </h1>
            <p class="text-xl md:text-2xl font-extralight italic text-gray-200">
                Manage dining areas, reservations, and users
            </p>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const slides = ['item1','item2','item3'];
    let index = 0;

    setInterval(() => {
        slides.forEach((id,i)=>{
            document.getElementById(id).classList.toggle('hidden', i !== index);
        });
        index = (index + 1) % slides.length;
    }, 5000);
});
</script>

<section class="py-16 bg-base-300">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-serif font-light text-center mb-12 text-warning">
            — Dining Area Overview —
        </h2>

        <div class="flex flex-wrap justify-center gap-6">
            <?php
            $dining_areas = [
                [
                    'title' => 'The Chef’s Table',
                    'motto' => 'Where culinary art meets intimacy.',
                    'description' => 'Experience culinary artistry up close. A prime spot near the open-concept kitchen.',
                    'capacity' => 4,
                    'feature' => 'Exclusive',
                    'image' => '../assets/table1.jpg'
                ],
                [
                    'title' => 'Intimate Bistro Tables',
                    'motto' => 'Perfect moments for two.',
                    'description' => 'Perfect for couples and small parties. Cozy seating with romantic ambient lighting.',
                    'capacity' => 2,
                    'feature' => 'Intimate',
                    'image' => '../assets/table2.jpg'
                ],
                [
                    'title' => 'The Grand Salon',
                    'motto' => 'Elegance for every grand occasion.',
                    'description' => 'A fully private, soundproof room ideal for larger corporate or family celebrations.',
                    'capacity' => 12,
                    'feature' => 'Luxury',
                    'image' => '../assets/table3.jpg'
                ],
                [
                    'title' => 'Al Fresco Terrace',
                    'motto' => 'Dine under the open sky.',
                    'description' => 'Enjoy the evening air on our beautiful outdoor terrace with a breathtaking view.',
                    'capacity' => 8,
                    'feature' => 'Outdoor',
                    'image' => '../assets/table4.jpg'
                ],
                [
                    'title' => 'Main Dining Hall',
                    'motto' => 'Classic comfort, timeless taste.',
                    'description' => 'Comfortable and versatile seating for walk-ins and standard reservations.',
                    'capacity' => 10,
                    'feature' => 'Standard',
                    'image' => '../assets/table5.jpg'
                ],
            ];

            foreach ($dining_areas as $area):
            ?>
            <div class="card w-full sm:w-80 bg-black text-gray-200 border border-gray-700 shadow-xl">
                
                <figure class="h-28">
                    <img src="<?= htmlspecialchars($area['image']) ?>"
                        alt="<?= htmlspecialchars($area['title']) ?>"
                        class="w-full h-full object-cover">
                </figure>

                <div class="card-body p-4 space-y-1">
                    <h2 class="text-warning text-xl font-serif">
                        <?= htmlspecialchars($area['title']) ?>
                    </h2>

                    <p class="text-xs italic text-warning/80 overflow-hidden"
                        style="display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;">
                        “<?= htmlspecialchars($area['motto']) ?>”
                    </p>

                    <div class="badge badge-outline badge-warning badge-sm w-fit mt-1">
                        <?= htmlspecialchars($area['feature']) ?>
                    </div>

                    <p class="text-sm text-gray-300 leading-snug overflow-hidden"
                        style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                        <?= htmlspecialchars($area['description']) ?>
                    </p>

                    <p class="text-sm text-gray-300 mt-1">
                        <span class="text-white font-medium">
                            <?= (int)$area['capacity'] ?> Guests
                        </span>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="footer footer-center p-3 bg-neutral text-neutral-content text-xs opacity-75">
    <p>© 2025 Savory Spot — Admin Panel</p>
</div>

<footer class="footer p-10 bg-neutral text-neutral-content grid grid-cols-2 sm:grid-cols-4 gap-8 border-t border-warning">
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
    <p>Copyright © 2025 — All rights reserved by Savory Spot Restaurant Booking System</p>
</div>

<?php 
include(__DIR__ . '/../includes/footer.php'); 
?>