<?php 
include(__DIR__ . '/../includes/header.php'); 
?>

<div class="navbar bg-neutral shadow-lg sticky top-0 z-50">
    <div class="navbar-start">
        <a class="btn btn-ghost text-2xl font-serif font-bold text-warning hover:bg-transparent">Savory Spot</a>
    </div>

    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
            <li><a href="#home" class="font-medium text-lg text-warning hover:text-warning">Home</a></li>
            <li><a href="#dining" class="font-medium text-lg text-warning hover:text-warning">Dining Areas</a></li>
            <li><a href="reservations.php" class="font-medium text-lg text-warning hover:text-warning" id="reservation">Reservations</a></li>
            <li><a href="#contact" class="font-medium text-lg text-warning hover:text-warning">Contact</a></li>
        </ul>
    </div>

    <div class="navbar-end">
        <a href="../handlers/login.php" class="btn btn-warning btn-sm font-semibold">Logout</a>
    </div>
</div>

<section id="home" class="relative">
    <div class="carousel w-full h-[40vh] md:h-[60vh] relative">
        <div id="item1" class="carousel-item w-full">
            <img src="../assets/bg.jpg" class="w-full object-cover brightness-75" alt="Fine Dining Room" />
        </div>
        <div id="item2" class="carousel-item w-full hidden">
            <img src="../assets/bg1.jpg" class="w-full object-cover brightness-75" alt="Gourmet Meal" />
        </div>
        <div id="item3" class="carousel-item w-full hidden">
            <img src="../assets/bg2.jpg" class="w-full object-cover brightness-75" alt="Elegant Table Setting" />
        </div>
    </div>

    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30">
        <div class="text-center text-white p-4 max-w-4xl">
            <h1 class="text-5xl md:text-7xl font-serif font-light mb-4 tracking-tight">Savory Spot: Reserve Your Table</h1>
            <p class="text-xl md:text-2xl mb-8 font-extralight italic">An Exquisite Culinary Experience. Book Your Spot Today.</p>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const items = [
        document.getElementById('item1'),
        document.getElementById('item2'),
        document.getElementById('item3')
    ];
    let currentIndex = 0;

    function showSlide(index) {
        items.forEach((item, i) => {
            if (i === index) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % items.length;
        showSlide(currentIndex);
    }

    showSlide(currentIndex);
    setInterval(nextSlide, 5000);
});
</script>

<section id="dining" class="py-20 bg-base-300">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-serif font-light text-center mb-16 text-warning">— Dining Areas & Tables —</h2>
        
        <div class="flex flex-wrap justify-center gap-10">
            <?php
            $dining_areas = [
                [
                    'title' => 'The Chef’s Table', 
                    'description' => 'Experience culinary artistry up close. A prime spot near the open-concept kitchen.', 
                    'capacity' => 4, 
                    'feature' => 'Exclusive',
                    'image' => '../assets/table1.jpg' 
                ],
                [
                    'title' => 'Intimate Bistro Tables', 
                    'description' => 'Perfect for couples and small parties. Cozy seating with romantic ambient lighting.', 
                    'capacity' => 2, 
                    'feature' => 'Intimate',
                    'image' => '../assets/table2.jpg' 
                ],
                [
                    'title' => 'The Grand Salon', 
                    'description' => 'A fully private, soundproof room ideal for larger corporate or family celebrations.', 
                    'capacity' => 12, 
                    'feature' => 'Luxury',
                    'image' => '../assets/table3.jpg' 
                ],
                [
                    'title' => 'Al Fresco Terrace', 
                    'description' => 'Enjoy the evening air on our beautiful outdoor terrace with a breathtaking view.', 
                    'capacity' => 8, 
                    'feature' => 'Outdoor',
                    'image' => '../assets/table4.jpg' 
                ],
                [
                    'title' => 'Main Dining Hall', 
                    'description' => 'Comfortable and versatile seating for walk-ins and standard reservations.', 
                    'capacity' => 10,
                    'feature' => 'Standard',
                    'image' => '../assets/table5.jpg' 
                ],
            ];

            foreach($dining_areas as $area):
            ?>
            <div class="card w-full sm:w-96 bg-black shadow-2xl overflow-hidden transition-all duration-500 hover:shadow-[0_0_40px_rgba(255,193,7,0.3)] border border-gray-700">
                <figure class="h-32">
                    <img src="<?= htmlspecialchars($area['image']) ?>" alt="<?= htmlspecialchars($area['title']) ?>" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"/>
                </figure>
                <div class="card-body p-8">
                    <div class="flex justify-between items-start">
                        <h2 class="card-title text-3xl font-serif font-normal text-warning">
                            <?= htmlspecialchars($area['title']) ?>
                        </h2>
                        <div class="badge badge-outline badge-warning text-sm mt-1"><?= htmlspecialchars($area['feature']) ?></div>
                    </div>
                    
                    <p class="text-base text-gray-400 mb-4 font-light italic h-12"><?= htmlspecialchars($area['description']) ?></p>
                    
                    <div class="flex justify-end pt-3">
                        <div class="flex items-center text-md text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20v-2c0-.523-.328-.962-.75-1.114L15 16m-2-12v2m0 8v2m-6-8h2m-2 4h2M12 21c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8z" />
                            </svg>
                            <?= (int)$area['capacity'] ?> Guests
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

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