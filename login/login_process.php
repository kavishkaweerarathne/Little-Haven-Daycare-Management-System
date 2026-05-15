<?php
include '../config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = $_POST['password'];

    // Hardcoded Admin Check
    if ($username === 'admin@gmail.com' && $password === '0000') {
        $admin_check = mysqli_query($con, "SELECT id, fullname FROM users WHERE email = 'admin@gmail.com'");
        if ($admin_row = mysqli_fetch_assoc($admin_check)) {
            $_SESSION['user_id'] = $admin_row['id'];
            $_SESSION['fullname'] = $admin_row['fullname'];
        } else {
            $_SESSION['user_id'] = '1'; // Fallback if record somehow missing
            $_SESSION['fullname'] = 'Administrator';
        }
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
            $_SESSION['phone'] = $user['phone'];
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
            header("Location: login.php?error=" . urlencode('Invalid password! Please try again.'));
            exit();
        }
    } else {
        header("Location: login.php?error=" . urlencode('User not found! Please register or check your credentials.'));
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
