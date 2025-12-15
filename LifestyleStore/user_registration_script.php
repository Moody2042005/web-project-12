<?php
session_start();
require 'connection.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $hashed_password = md5(md5($password));
    
    $check_query = "SELECT id FROM users WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result) == 0) {
        $query = "INSERT INTO users (name, username, password, contact, city, address) 
                  VALUES ('$name', '$username', '$hashed_password', '$contact', '$city', '$address')";
        
        if(mysqli_query($conn, $query)) {
            $user_id = mysqli_insert_id($conn);
            $_SESSION['id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['name'] = $name;
            header('location: products.php');
        } else {
            header('location: signup.php?error=' . urlencode(mysqli_error($conn)));
        }
    } else {
        header('location: signup.php?error=Username already exists');
    }
    exit();
}

header('location: signup.php');
exit();
?>