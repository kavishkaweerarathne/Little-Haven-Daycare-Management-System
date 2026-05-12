<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Little Haven Elite</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="register.css">
</head>
<body>

    <div class="login-bg"></div>
    <div class="login-overlay"></div>

    <main class="register-container">
        <div class="register-card">
            <!-- Logo -->
            <a href="../home/home.php" class="logo">
                <i class="fas fa-hands-holding-child logo-icon"></i>
                <span class="logo-text">Little Haven</span>
            </a>

            <div class="register-header">
                <h1>Join Our Community</h1>
                <p>Create an account to manage your child's journey or your teaching schedule.</p>
            </div>

            <form action="register_process.php" method="POST">
                <!-- Role Selection -->
                <div class="role-selection">
                    <label class="role-option">
                        <input type="radio" name="role" value="parent" checked>
                        <div class="role-card">
                            <i class="fas fa-user-group"></i>
                            <span>Parent</span>
                        </div>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="staff">
                        <div class="role-card">
                            <i class="fas fa-user-tie"></i>
                            <span>Staff</span>
                        </div>
                    </label>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="fullname" name="fullname" class="form-control" placeholder="John Doe" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" required>
                        </div>
                        <span class="error-text" id="email-error"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="07XXXXXXXX" required>
                        </div>
                        <span class="error-text" id="phone-error"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <span class="error-text" id="password-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-shield-halved"></i>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <span class="error-text" id="confirm_password-error"></span>
                    </div>
                </div>

                <button type="submit" class="btn-register">Create Account</button>
            </form>

            <div class="register-footer">
                <p>Already have an account? <a href="../login/login.php">Sign In</a></p>
                <a href="../home/home.php" class="back-home">
                    <i class="fas fa-arrow-left"></i> Back to Homepage
                </a>
            </div>
        </div>
    </main>

    <script src="register.js"></script>
</body>
</html>
