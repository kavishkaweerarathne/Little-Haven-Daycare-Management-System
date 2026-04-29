<?php 
include '../config.php'; 
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'parent') {
        header("Location: ../parent/parent_dashboard.php");
    } elseif ($_SESSION['role'] === 'staff') {
        header("Location: ../staff/staff_dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Little Haven Elite</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <div class="login-bg"></div>
    <div class="login-overlay"></div>

    <main class="login-container">
        <div class="login-card">
            <!-- Logo -->
            <a href="../home/home.php" class="logo">
                <i class="fas fa-hands-holding-child logo-icon"></i>
                <span>Little Haven</span>
            </a>

            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Please enter your credentials to access your dashboard.</p>
            </div>

            <form action="login_process.php" method="POST">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="admin@littlehaven.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="form-utils">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <div class="login-footer" style="margin-top: 25px; font-size: 0.95rem; color: var(--text-muted);">
                <p>Don't have an account? <a href="../register/register.php" style="color: var(--primary-dark); text-decoration: none; font-weight: 700;">Register Now</a></p>
            </div>

            <a href="../home/home.php" class="back-home" style="margin-top: 20px;">
                <i class="fas fa-arrow-left"></i> Back to Homepage
            </a>
        </div>
    </main>

</body>
</html>
