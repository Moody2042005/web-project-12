<?php
// test_login.php - Minimal version
session_start();

// Hardcoded login check
if(isset($_POST['login'])) {
    if($_POST['username'] == 'john_doe' && $_POST['password'] == 'password123') {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = 'john_doe';
        header('Location: test_products.php');
        exit;
    }
}

// Simple HTML form
?>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit" name="login">Login</button>
</form>