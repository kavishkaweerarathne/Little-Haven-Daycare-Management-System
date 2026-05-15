<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Archive the item first
    $stmt_archive = $con->prepare("INSERT INTO inventory_archive (id, item_name, category, unit, quantity, supplier_name) SELECT id, item_name, category, unit, quantity, supplier_name FROM inventory WHERE id = ?");
    $stmt_archive->bind_param("i", $id);
    $stmt_archive->execute();

    // Delete from inventory
    $stmt = $con->prepare("DELETE FROM inventory WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Item deleted and archived successfully!'); window.location.href='inventory_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting item: " . $con->error . "'); window.location.href='inventory_dashboard.php';</script>";
    }
} else {
    header("Location: inventory_dashboard.php");
}
?>
