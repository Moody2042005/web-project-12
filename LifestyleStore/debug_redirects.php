<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require 'connection.php';

echo "<h2>Session Debug Info</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n";
echo "Session Data:\n";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Quick Links Test:</h2>";
echo "<ul>";
echo "<li><a href='index.php'>Home</a></li>";
echo "<li><a href='login.php'>Login</a></li>";
echo "<li><a href='signup.php'>Signup</a></li>";
echo "<li><a href='products.php?category=all'>All Products</a></li>";
echo "<li><a href='products.php?category=Watch'>Watches</a></li>";
echo "<li><a href='products.php?category=Camera'>Cameras</a></li>";
echo "<li><a href='products.php?category=Clothing'>Clothing</a></li>";
echo "</ul>";
?>