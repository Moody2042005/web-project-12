<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
$_SESSION = array();
session_destroy();
header('location: index.php');
exit();
?>