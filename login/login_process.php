<?php
include '../config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = $_POST['password'];

    // Check if user exists (using email as username)
    $sql = "SELECT * FROM users WHERE email = '$username'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'parent') {
                header("Location: ../parent/parent_dashboard.php");
            } elseif ($user['role'] === 'staff') {
                header("Location: ../staff/staff_dashboard.php");
            } else {
                // Default redirection if role is unknown
                header("Location: ../home/home.php");
            }
            exit();
        } else {
            echo "<script>alert('Invalid password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.history.back();</script>";
    }
} else {
    header("Location: login.php");
    exit();
}
?>
