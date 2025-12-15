<?php
// Force session reset
session_start();
session_unset();
session_destroy();
session_write_close();
setcookie(session_name(), '', 0, '/');

// Clear all cookies
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}

echo "<h2>✅ Session and Cookies Cleared!</h2>";
echo "<p>All session data and cookies have been removed.</p>";
echo "<p><a href='index.php'>Go to Homepage</a></p>";
echo "<p><a href='login.php'>Go to Login</a></p>";

// Auto-redirect after 2 seconds
echo '<meta http-equiv="refresh" content="2;url=index.php">';
?>