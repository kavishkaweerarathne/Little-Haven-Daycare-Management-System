<?php include '../config.php'; ?>
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
            <a href="home.php" class="logo">
                <img src="../assets/logo_teal.png" alt="Little Haven Logo">
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

            <a href="home.php" class="back-home">
                <i class="fas fa-arrow-left"></i> Back to Homepage
            </a>
        </div>
    </main>

</body>
</html>
