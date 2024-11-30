<?php
// db.php

// Database credentials
$servername = "localhost"; // Database host (use 'localhost' for local development)
$username = "root";        // Database username (default for local MySQL)
$password = "";            // Database password (leave empty if using default XAMPP setup)
$dbname = "iisadfc";       // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optionally, you can set the character set to utf8mb4 for supporting wide characters
$conn->set_charset("utf8mb4");

?>
