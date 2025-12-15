<?php
session_start();
if(!isset($_SESSION['loggedin'])) {
    header('Location: test_login.php');
    exit;
}
echo "<h1>Welcome " . $_SESSION['username'] . "</h1>";
echo "<p>Products page works!</p>";
echo "<a href='test_logout.php'>Logout</a>";
?>