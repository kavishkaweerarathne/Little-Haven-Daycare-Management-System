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
            <p class="active" data-tab="dashboard"><i class="fas fa-chart-line"></i> Dashboard</p>
            <p data-tab="staff"><i class="fas fa-users"></i> Manage Staff</p>
            <p data-tab="parents"><i class="fas fa-user-group"></i> Manage Parents</p>
            <p data-tab="children"><i class="fas fa-baby"></i> Manage Children</p>
            <p data-tab="billing"><i class="fas fa-file-invoice-dollar"></i> Billing and Payment</p>
            <p data-tab="inventory"><i class="fas fa-boxes-stacked"></i> Inventory</p>
            <p data-tab="settings"><i class="fas fa-gear"></i> Settings</p>
        </nav>
    </div>
    <div class="main-content">
        <div class="header">
            <h1 id="tab-title">Admin Overview</h1>
            <a href="../login/logout.php" class="logout-btn">Logout</a>
        </div>
        
        <!-- Dashboard Section -->
        <div id="dashboard-tab" class="tab-content active">
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

        <!-- Manage Staff Section -->
        <div id="staff-tab" class="tab-content" style="display: none;">
            <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h2>Staff Directory</h2>
                    <button class="logout-btn" style="background: var(--primary);">+ Add Staff</button>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f3f4f6;">
                            <th style="padding: 1rem;">Name</th>
                            <th style="padding: 1rem;">Email</th>
                            <th style="padding: 1rem;">Phone</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include '../config.php';
                        $sql = "SELECT * FROM users WHERE role = 'staff'";
                        $result = mysqli_query($con, $sql);
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr style='border-bottom: 1px solid #f3f4f6;'>";
                            echo "<td style='padding: 1rem;'>".$row['fullname']."</td>";
                            echo "<td style='padding: 1rem;'>".$row['email']."</td>";
                            echo "<td style='padding: 1rem;'>".$row['phone']."</td>";
                            echo "<td style='padding: 1rem;'>
                                    <button style='border:none; background:none; color: #4f46e5; cursor:pointer; margin-right: 10px;'><i class='fas fa-edit'></i></button>
                                    <button style='border:none; background:none; color: #ef4444; cursor:pointer;'><i class='fas fa-trash'></i></button>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Manage Parents Section -->
        <div id="parents-tab" class="tab-content" style="display: none;">
            <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h2>Parent Directory</h2>
                    <button class="logout-btn" style="background: var(--primary);">+ Add Parent</button>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f3f4f6;">
                            <th style="padding: 1rem;">Name</th>
                            <th style="padding: 1rem;">Email</th>
                            <th style="padding: 1rem;">Phone</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM users WHERE role = 'parent'";
                        $result = mysqli_query($con, $sql);
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr style='border-bottom: 1px solid #f3f4f6;'>";
                            echo "<td style='padding: 1rem;'>".$row['fullname']."</td>";
                            echo "<td style='padding: 1rem;'>".$row['email']."</td>";
                            echo "<td style='padding: 1rem;'>".$row['phone']."</td>";
                            echo "<td style='padding: 1rem;'>
                                    <button style='border:none; background:none; color: #4f46e5; cursor:pointer; margin-right: 10px;'><i class='fas fa-edit'></i></button>
                                    <button style='border:none; background:none; color: #ef4444; cursor:pointer;'><i class='fas fa-trash'></i></button>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Custom JS -->
    <script src="admin_dashboard.js"></script>
</body>
</html>
