<?php
include 'config.php';

$fullname = 'System Administrator';
$email = 'admin@gmail.com';
$phone = '0000000000';
$password = '0000';
$role = 'admin';

// Check if admin already exists
$check_sql = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($con, $check_sql);

if (mysqli_num_rows($result) === 0) {
    $sql = "INSERT INTO users (fullname, email, phone, password, role) VALUES ('$fullname', '$email', '$phone', '$password', '$role')";
    if (mysqli_query($con, $sql)) {
        echo "Admin account created successfully!";
    } else {
        echo "Error: " . mysqli_error($con);
    }
} else {
    echo "Admin account already exists.";
}
?>
