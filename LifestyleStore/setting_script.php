<?php
session_start();
require 'connection.php';

if(!isset($_SESSION['id'])) {
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['id'];

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['update_profile'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $contact = mysqli_real_escape_string($conn, $_POST['contact']);
        $city = mysqli_real_escape_string($conn, $_POST['city']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        
        $query = "UPDATE users SET name='$name', contact='$contact', city='$city', address='$address' WHERE id='$user_id'";
        
        if(mysqli_query($conn, $query)) {
            $_SESSION['name'] = $name;
            header('location: settings.php?success=Profile updated');
        } else {
            header('location: settings.php?error=' . urlencode(mysqli_error($conn)));
        }
        exit();
    }
}

header('location: settings.php');
exit();
?>