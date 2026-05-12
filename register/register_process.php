<?php
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $role = mysqli_real_escape_string($con, $_POST['role']);
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Restricted Roles Check
    $allowed_roles = ['parent', 'staff'];
    if (!in_array($role, $allowed_roles)) {
        die("<script>alert('Unauthorized role selection!'); window.history.back();</script>");
    }

    // Basic Validation
    if ($password !== $confirm_password) {
        die("<script>alert('Passwords do not match!'); window.history.back();</script>");
    }

    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $check_email);

    if (mysqli_num_rows($result) > 0) {
        die("<script>alert('Email already registered!'); window.history.back();</script>");
    }


    // Insert user into database
    $sql = "INSERT INTO users (fullname, email, phone, password, role) VALUES ('$fullname', '$email', '$phone', '$password', '$role')";

    if (mysqli_query($con, $sql)) {
        echo "<script>alert('Registration successful! Please login.'); window.location.href='../login/login.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
} else {
    header("Location: register.php");
    exit();
}
?>
