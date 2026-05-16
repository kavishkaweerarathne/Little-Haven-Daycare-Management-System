<?php
session_start(); // start session
session_unset(); // unset all session variables
session_destroy(); // destroy session
header("Location: login.php"); // redirect to login page
exit(); // exit
?>
