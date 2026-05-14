<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);

    // Check if email is already taken by another user
    $check_email = "SELECT id FROM users WHERE email = '$email' AND id != '$user_id'";
    $result = mysqli_query($con, $check_email);
    if (mysqli_num_rows($result) > 0) {
        header("Location: parent_dashboard.php?tab=settings&error=Email address is already in use by another account.");
        exit();
    }

    // Update database
    $sql = "UPDATE users SET fullname = '$fullname', email = '$email', phone = '$phone' WHERE id = '$user_id'";
    
    if (mysqli_query($con, $sql)) {
        // Update session variables
        $_SESSION['fullname'] = $fullname;
        $_SESSION['email'] = $email;
        $_SESSION['phone'] = $phone;
        
        header("Location: parent_dashboard.php?tab=settings&success=Profile updated successfully!");
    } else {
        header("Location: parent_dashboard.php?tab=settings&error=Error updating profile: " . mysqli_error($con));
    }
    exit();
} else {
    header("Location: parent_dashboard.php?tab=settings");
    exit();
}
?>
