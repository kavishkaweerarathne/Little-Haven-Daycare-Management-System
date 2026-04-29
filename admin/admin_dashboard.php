<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <div class="sidebar">
        <h2><i class="fas fa-hands-holding-child"></i> Little Haven</h2>
        <nav>
            <p class="active"><i class="fas fa-chart-line"></i> Dashboard</p>
            <p><i class="fas fa-users"></i> Manage Staff</p>
            <p><i class="fas fa-user-group"></i> Manage Parents</p>
            <p><i class="fas fa-baby"></i> Manage Children</p>
            <p><i class="fas fa-file-invoice-dollar"></i> Billing and Payment</p>
            <p><i class="fas fa-boxes-stacked"></i> Inventory</p>
            <p><i class="fas fa-gear"></i> Settings</p>
        </nav>
    </div>
    <div class="main-content">
        <div class="header">
            <div>
                <h1>Admin Overview</h1>
                <p style="color: #6b7280;">Welcome back, Administrator</p>
            </div>
            <a href="../login/logout.php" class="logout-btn">Logout</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #6366f1;"><i class="fas fa-users"></i></div>
                <div>
                    <h3 style="margin:0; font-size: 0.9rem; color: #6b7280;">Total Staff</h3>
                    <p style="margin:0; font-size: 1.5rem; font-weight: 700;">12</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #10b981;"><i class="fas fa-user-group"></i></div>
                <div>
                    <h3 style="margin:0; font-size: 0.9rem; color: #6b7280;">Total Parents</h3>
                    <p style="margin:0; font-size: 1.5rem; font-weight: 700;">48</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #f59e0b;"><i class="fas fa-baby"></i></div>
                <div>
                    <h3 style="margin:0; font-size: 0.9rem; color: #6b7280;">Active Kids</h3>
                    <p style="margin:0; font-size: 1.5rem; font-weight: 700;">56</p>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <h2>System Status</h2>
            <p>Everything is running smoothly. No pending approvals today.</p>
        </div>
    </div>
    <!-- Custom JS -->
    <script src="admin_dashboard.js"></script>
</body>
</html>
