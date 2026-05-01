<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = mysqli_real_escape_string($con, $_GET['status']);
    
    if ($con->query("UPDATE inventory_orders SET status = '$status' WHERE id = $id")) {
        echo "<script>alert('Order status updated to $status!'); window.location.href='inventory_dashboard.php?tab=orders';</script>";
    } else {
        echo "<script>alert('Error updating order: " . $con->error . "'); window.location.href='inventory_dashboard.php?tab=orders';</script>";
    }
} else {
    header("Location: inventory_dashboard.php?tab=orders");
}
?>
