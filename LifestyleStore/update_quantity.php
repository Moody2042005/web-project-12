<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

if(!isset($_SESSION['id'])) {
    header('location: login.php');
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $user_id = $_SESSION['id'];
    $item_id = mysqli_real_escape_string($conn, $_POST['item_id']);
    $quantity = intval($_POST['quantity']);
    
    if($quantity < 1) $quantity = 1;
    if($quantity > 10) $quantity = 10;
    
    $update_query = "UPDATE users_items SET quantity = '$quantity' 
WHERE user_id = '$user_id' AND item_id = '$item_id' AND status = 'Added to cart'";
    
    if(mysqli_query($conn, $update_query)) {
        $_SESSION['message'] = "Quantity updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating quantity.";
    }
}

header('location: cart.php');
exit();
?>