<?php
// Database connection configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Your MySQL password, often empty for XAMPP/WAMP
define('DB_NAME', 'savory_spot_restaurant_booking_system'); // Database name from your image

// Establish the connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Function to safely close the connection
function close_db_connection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>