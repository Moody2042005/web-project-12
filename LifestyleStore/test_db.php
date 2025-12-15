<?php
require 'connection.php';
echo "<h2>Database Test</h2>";

// Test users table
$result = mysqli_query($conn, "SELECT * FROM users LIMIT 5");
echo "<h3>Users in database:</h3>";
while($row = mysqli_fetch_assoc($result)) {
    echo "ID: {$row['id']}, Username: {$row['username']}, Name: {$row['name']}<br>";
}

// Test items table
$result = mysqli_query($conn, "SELECT * FROM items LIMIT 5");
echo "<h3>Products in database:</h3>";
while($row = mysqli_fetch_assoc($result)) {
    echo "ID: {$row['id']}, Name: {$row['name']}, Price: {$row['price']}<br>";
}
?>