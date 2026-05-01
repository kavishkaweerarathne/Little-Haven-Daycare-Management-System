<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($con->query("DELETE FROM suppliers WHERE id = $id")) {
        echo "<script>alert('Supplier deleted successfully!'); window.location.href='inventory_dashboard.php?tab=suppliers';</script>";
    } else {
        echo "<script>alert('Error deleting supplier: " . $con->error . "'); window.location.href='inventory_dashboard.php?tab=suppliers';</script>";
    }
} else {
    header("Location: inventory_dashboard.php?tab=suppliers");
}
?>
