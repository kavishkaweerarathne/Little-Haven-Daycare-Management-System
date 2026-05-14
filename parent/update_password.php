<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch current password from DB
    $sql = "SELECT password FROM users WHERE id = '$user_id'";
    $result = mysqli_query($con, $sql);
    $user = mysqli_fetch_assoc($result);

    // 1. Check if old password matches
    if ($old_password !== $user['password']) {
        header("Location: parent_dashboard.php?tab=settings&set_tab=security&error=Current password is incorrect.");
        exit();
    }

    // 2. Check if new passwords match
    if ($new_password !== $confirm_password) {
        header("Location: parent_dashboard.php?tab=settings&set_tab=security&error=New passwords do not match.");
        exit();
    }

    // 3. Update password (assuming plain text as per project pattern so far)
    $update_sql = "UPDATE users SET password = '$new_password' WHERE id = '$user_id'";
    
    if (mysqli_query($con, $update_sql)) {
        header("Location: parent_dashboard.php?tab=settings&set_tab=security&success=Password updated successfully!");
    } else {
        header("Location: parent_dashboard.php?tab=settings&set_tab=security&error=Error updating password: " . mysqli_error($con));
    }
    exit();
} else {
    header("Location: parent_dashboard.php?tab=settings");
    exit();
}
?>
