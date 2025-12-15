<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require 'connection.php';

// Log admin logout
if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    $log_query = "INSERT INTO admin_logs (admin_id, action, details) 
VALUES ('{$_SESSION['id']}', 'Admin Logout', 'Admin logged out of system')";
    mysqli_query($conn, $log_query);
}

// Clear session
$_SESSION = array();
session_destroy();

// Redirect to login
header('Location: login.php');
exit();
?>