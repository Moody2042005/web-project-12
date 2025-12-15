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
    
    $check_query = "SELECT id FROM users_items WHERE user_id = '$user_id' AND item_id = '$item_id' AND status = 'Added to cart'";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result) == 0) {
        $insert_query = "INSERT INTO users_items (user_id, item_id, status) VALUES ('$user_id', '$item_id', 'Added to cart')";
        if(mysqli_query($conn, $insert_query)) {
            $_SESSION['message'] = "Item added to cart successfully!";
        } else {
            $_SESSION['message'] = "Error adding item to cart.";
        }
    } else {
        $_SESSION['message'] = "Item is already in your cart!";
    }
}

header('location: products.php');
exit();
?>