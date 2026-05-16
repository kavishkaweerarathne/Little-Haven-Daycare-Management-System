<?php
include '../config.php';

//Registration Processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $role = mysqli_real_escape_string($con, $_POST['role']);   // role selection
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);   // full name
    $email = mysqli_real_escape_string($con, $_POST['email']);   // email
    $phone = mysqli_real_escape_string($con, $_POST['phone']);   // phone number
    $password = $_POST['password'];   // password
    $confirm_password = $_POST['confirm_password'];   // confirm password

    // Restricted Roles Check
    $allowed_roles = ['parent', 'staff'];   // allowed roles
    if (!in_array($role, $allowed_roles)) {  // check if role is allowed
        header("Location: register.php?error=" . urlencode('Unauthorized role selection!'));
        exit();
    }

    // Basic Validation
    // password validation
    if ($password !== $confirm_password) {
        header("Location: register.php?error=" . urlencode('Passwords do not match!'));
        exit();
    }

    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $check_email);

    if (mysqli_num_rows($result) > 0) {
        header("Location: register.php?error=" . urlencode('Email already registered!'));
        exit();
    }


    // Insert user into database
    $sql = "INSERT INTO users (fullname, email, phone, password, role) VALUES ('$fullname', '$email', '$phone', '$password', '$role')";

    if (mysqli_query($con, $sql)) {  // check if user is inserted successfully
        header("Location: ../login/login.php?success=" . urlencode('Registration successful! Please login.'));
        exit();
    } else {  // if user is not inserted successfully
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
} else {
    header("Location: register.php");
    exit();
}
?>
