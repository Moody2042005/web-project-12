<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

if(!isset($_SESSION['id'])) {
    header('location: login.php');
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_id'])) {
    $user_id = $_SESSION['id'];
    $item_id = mysqli_real_escape_string($conn, $_POST['item_id']);
    
    $delete_query = "DELETE FROM users_items WHERE user_id = '$user_id' AND item_id = '$item_id' AND status = 'Added to cart'";
    
    if(mysqli_query($conn, $delete_query)) {
        $_SESSION['message'] = "Item removed from cart successfully!";
    } else {
        $_SESSION['message'] = "Error removing item from cart.";
    }
}

header('location: cart.php');
exit();
?>