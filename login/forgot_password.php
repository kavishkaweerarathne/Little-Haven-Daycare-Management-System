<?php 
include '../config.php'; 
session_start();

$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    // In a real app, you would check if the email exists and send a real code.
    // For this task, we will just show the success message as requested.
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Little Haven</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom CSS (Reusing login styles) -->
    <link rel="stylesheet" href="login.css">
    <style>
        .forgot-card {
            max-width: 500px;
            margin: 0 auto;
        }
        .instruction-text {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 25px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="login-bg"></div>
    <div class="login-overlay"></div>

    <main class="login-container">
        <div class="login-card forgot-card">
            <!-- Logo -->
            <a href="../home/home.php" class="logo">
                <i class="fas fa-hands-holding-child logo-icon"></i>
                <span>Little Haven</span>
            </a>

            <div class="login-header">
                <h1>Reset Password</h1>
                <p>Lost your access? No worries, we'll help you out.</p>
            </div>

            <p class="instruction-text">
                Enter your registered email address below. We will send a secure verification code to reset your password.
            </p>

            <form action="" method="POST" id="forgotForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="example@mail.com" required>
                    </div>
                </div>

                <button type="submit" class="btn-login" style="margin-top: 10px;">Send Reset Code</button>
            </form>

            <div class="login-footer" style="margin-top: 30px; text-align: center;">
                <a href="login.php" style="color: var(--primary-dark); text-decoration: none; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fas fa-arrow-left" style="font-size: 0.8rem;"></i> Back to Login
                </a>
            </div>
        </div>
    </main>

    <?php if ($success): ?>
    <script>
        Swal.fire({
            title: 'Code Sent!',
            text: 'A secure verification code has been sent to your email address. Please check your inbox.',
            icon: 'success',
            confirmButtonColor: '#0097A7',
            confirmButtonText: 'Great, I\'ll check!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'login.php';
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>
