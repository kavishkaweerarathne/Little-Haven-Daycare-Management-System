<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $user_id = $_SESSION['user_id'];
    
    // Always update name
    $query = "UPDATE users SET fullname = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $fullname, $user_id);
    
    if (!$stmt->execute()) {
        echo "<script>alert('Error updating name: " . $con->error . "'); window.history.back();</script>";
        exit();
    }
    $_SESSION['fullname'] = $fullname;

    // Password change logic
    if (!empty($_POST['new_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password)) {
            echo "<script>alert('Please enter your current password to change it.'); window.history.back();</script>";
            exit();
        }

        if ($new_password !== $confirm_password) {
            echo "<script>alert('New passwords do not match!'); window.history.back();</script>";
            exit();
        }

        // Verify current password
        $auth_stmt = $con->prepare("SELECT password FROM users WHERE id = ?");
        $auth_stmt->bind_param("i", $user_id);
        $auth_stmt->execute();
        $user_data = $auth_stmt->get_result()->fetch_assoc();

        if (password_verify($current_password, $user_data['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_pass_stmt = $con->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_pass_stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($update_pass_stmt->execute()) {
                echo "<script>alert('Settings and password updated successfully!'); window.location.href='inventory_dashboard.php?tab=settings';</script>";
            } else {
                echo "<script>alert('Error updating password: " . $con->error . "'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Current password incorrect!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Name updated successfully!'); window.location.href='inventory_dashboard.php?tab=settings';</script>";
    }
}
?>
