<?php
include '../config.php';
session_start();

// Security check: Only admins can delete users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Prevent admin from deleting themselves if they were in the table (though they aren't hardcoded here)
    // For now, just delete.
    
    $sql = "DELETE FROM users WHERE id = '$id'";
    
    if (mysqli_query($con, $sql)) {
        echo "<script>alert('User deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting user: " . mysqli_error($con) . "'); window.location.href='admin_dashboard.php';</script>";
    }
} else {
    header("Location: admin_dashboard.php");
}
?>
