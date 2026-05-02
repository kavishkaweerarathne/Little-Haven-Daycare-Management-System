<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$parent_id = $_SESSION['user_id'];

// Ensure the child belongs to this parent before deleting
$stmt = $con->prepare("DELETE FROM children WHERE id = ? AND parent_id = ?");
$stmt->bind_param("ii", $id, $parent_id);

if ($stmt->execute()) {
    echo "<script>alert('Child record deleted successfully.'); window.location.href='parent_dashboard.php?tab=children';</script>";
} else {
    echo "<script>alert('Error: " . $con->error . "'); window.history.back();</script>";
}
?>
