<?php
session_start();
require 'connection.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    
    if(empty($username) || empty($password)) {
        die("Username and password are required");
    }
    
    $hashed_password = md5(md5($password));
    
    $query = "SELECT id, name, username FROM users WHERE username = '$username' AND password = '$hashed_password'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];
        header('location: products.php');
        exit();
    } else {
        echo "<script>alert('Invalid username or password'); window.location='login.php';</script>";
    }
} else {
    header('Location: login.php');
    exit();
}
?>