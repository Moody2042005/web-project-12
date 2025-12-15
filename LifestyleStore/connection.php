<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "Moody2042005*";
$database = "store";
$port = 4306;

// Create connection with port
$conn = mysqli_connect($servername, $username, $password, $database, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8");
?>