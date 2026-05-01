<?php
include '../config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = $_POST['password'];

    // Hardcoded Admin Check
    if ($username === 'admin@gmail.com' && $password === '0000') {
        $_SESSION['user_id'] = 'admin';
        $_SESSION['fullname'] = 'Administrator';
        $_SESSION['email'] = 'admin@gmail.com';
        $_SESSION['role'] = 'admin';
        header("Location: ../admin/admin_dashboard.php");
        exit();
    }

    // Check if user exists in database
    $sql = "SELECT * FROM users WHERE email = '$username'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if ($password === $user['password']) {
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
            } elseif ($user['role'] === 'finance') {
                header("Location: ../finance/finance_dashboard.php");
            } elseif ($user['role'] === 'inventory') {
                header("Location: ../inventory/inventory_dashboard.php");
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
