<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

if(!isset($_SESSION['id'])) {
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['id'];
$clear_query = "DELETE FROM users_items WHERE user_id = '$user_id' AND status = 'Added to cart'";

if(mysqli_query($conn, $clear_query)) {
    $_SESSION['message'] = "Cart cleared successfully!";
} else {
    $_SESSION['message'] = "Error clearing cart.";
}

header('location: cart.php');
exit();
?>