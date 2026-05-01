<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $staff_id = $_SESSION['user_id'];
    
    // We only allow deleting children assigned to THIS staff member
    if ($con->query("DELETE FROM children WHERE id = $id AND staff_id = $staff_id")) {
        echo "<script>alert('Student removed from your class!'); window.location.href='staff_dashboard.php?tab=my_class';</script>";
    } else {
        echo "<script>alert('Error removing student: " . $con->error . "'); window.location.href='staff_dashboard.php?tab=my_class';</script>";
    }
} else {
    header("Location: staff_dashboard.php?tab=my_class");
}
?>
