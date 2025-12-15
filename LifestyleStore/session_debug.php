<?php
echo "<h2>Session Debug Information</h2>";

echo "<p>Session Status: " . session_status() . "</p>";
echo "<p>SID: " . session_id() . "</p>";

if (session_status() == PHP_SESSION_ACTIVE) {
    echo "<p style='color:green;'>Session is ACTIVE</p>";
    echo "<h3>Session Data:</h3>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
} else {
    echo "<p style='color:red;'>Session is NOT active</p>";
}

echo "<h3>Headers already sent?</h3>";
echo "<pre>";
print_r(headers_list());
echo "</pre>";
?>