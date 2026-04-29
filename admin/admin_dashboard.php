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
                    <div style="display: flex; gap: 1rem;">
                        <div style="position: relative;">
                            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                            <input type="text" id="staff-search" placeholder="Search staff..." style="padding: 10px 10px 10px 35px; border: 1px solid #e2e8f0; border-radius: 8px; width: 250px;">
                        </div>
                        <a href="add_user.php?role=staff" class="logout-btn" style="background: var(--primary); text-decoration: none;">+ Add Staff</a>
                    </div>
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
                                    <a href='view_user.php?id=".$row['id']."' class='action-btn view-btn' style='color: #10b981; margin-right: 10px;'><i class='fas fa-eye'></i></a>
                                    <a href='edit_user.php?id=".$row['id']."' class='action-btn edit-btn'><i class='fas fa-edit'></i></a>
                                    <a href='#' onclick='confirmDelete(".$row['id'].")' class='action-btn delete-btn'><i class='fas fa-trash'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Manage Children Section -->
        <div id="children-tab" class="tab-content" style="display: none;">
            <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h2>Children Registry</h2>
                    <div style="display: flex; gap: 1rem;">
                        <div style="position: relative;">
                            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                            <input type="text" id="children-search" placeholder="Search children..." style="padding: 10px 10px 10px 35px; border: 1px solid #e2e8f0; border-radius: 8px; width: 250px;">
                        </div>
                        <button class="logout-btn" style="background: var(--primary);">+ Add Child</button>
                    </div>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f3f4f6;">
                            <th style="padding: 1rem;">Name</th>
                            <th style="padding: 1rem;">Age</th>
                            <th style="padding: 1rem;">Gender</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM children";
                        $result = mysqli_query($con, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<tr style='border-bottom: 1px solid #f3f4f6;'>";
                                echo "<td style='padding: 1rem;'>".$row['name']."</td>";
                                echo "<td style='padding: 1rem;'>".$row['age']."</td>";
                                echo "<td style='padding: 1rem;'>".$row['gender']."</td>";
                                echo "<td style='padding: 1rem;'>
                                        <button class='action-btn view-btn' style='color: #10b981; margin-right: 10px;'><i class='fas fa-eye'></i></button>
                                        <button class='action-btn edit-btn'><i class='fas fa-edit'></i></button>
                                        <button class='action-btn delete-btn'><i class='fas fa-trash'></i></button>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='padding: 2rem; text-align: center; color: #64748b;'>No children records found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Billing and Payment Section -->
        <div id="billing-tab" class="tab-content" style="display: none;">
            <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h2>Billing Records</h2>
                    <button class="logout-btn" style="background: var(--primary);">+ Generate Invoice</button>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f3f4f6;">
                            <th style="padding: 1rem;">Child Name</th>
                            <th style="padding: 1rem;">Amount</th>
                            <th style="padding: 1rem;">Status</th>
                            <th style="padding: 1rem;">Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT b.*, c.name FROM billing b JOIN children c ON b.child_id = c.id";
                        $result = mysqli_query($con, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $status_color = $row['status'] == 'Paid' ? '#10b981' : '#f59e0b';
                                echo "<tr style='border-bottom: 1px solid #f3f4f6;'>";
                                echo "<td style='padding: 1rem;'>".$row['name']."</td>";
                                echo "<td style='padding: 1rem;'>LKR ".$row['amount']."</td>";
                                echo "<td style='padding: 1rem;'><span style='background: $status_color; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem;'>".$row['status']."</span></td>";
                                echo "<td style='padding: 1rem;'>".$row['due_date']."</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='padding: 2rem; text-align: center; color: #64748b;'>No billing records found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventory Section -->
        <div id="inventory-tab" class="tab-content" style="display: none;">
            <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h2>Inventory Management</h2>
                    <button class="logout-btn" style="background: var(--primary);">+ Add Item</button>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f3f4f6;">
                            <th style="padding: 1rem;">Item Name</th>
                            <th style="padding: 1rem;">Quantity</th>
                            <th style="padding: 1rem;">Category</th>
                            <th style="padding: 1rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM inventory";
                        $result = mysqli_query($con, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<tr style='border-bottom: 1px solid #f3f4f6;'>";
                                echo "<td style='padding: 1rem;'>".$row['item_name']."</td>";
                                echo "<td style='padding: 1rem;'>".$row['quantity']."</td>";
                                echo "<td style='padding: 1rem;'>".$row['category']."</td>";
                                echo "<td style='padding: 1rem;'>".$row['status']."</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='padding: 2rem; text-align: center; color: #64748b;'>No inventory items found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Settings Section -->
        <div id="settings-tab" class="tab-content" style="display: none;">
            <div style="background: white; padding: 2.5rem; border-radius: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); max-width: 700px;">
                <h2 style="margin-bottom: 2rem; color: var(--sidebar);">System Settings</h2>
                
                <form action="update_settings.php" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Daycare Name</label>
                            <input type="text" name="system_name" value="Little Haven Elite" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Admin Email</label>
                            <input type="email" name="admin_email" value="admin@gmail.com" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        </div>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <h3 style="font-size: 1.1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; margin-bottom: 1.5rem;">Security</h3>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Change Admin Password</label>
                            <input type="password" name="new_password" placeholder="Enter new password" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 0.5rem;">
                            <input type="password" name="confirm_password" placeholder="Confirm new password" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="logout-btn" style="background: var(--primary); width: auto;">Save All Settings</button>
                        <button type="reset" class="logout-btn" style="background: #94a3b8; width: auto;">Reset Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Custom JS -->
    <script src="admin_dashboard.js"></script>
</body>
</html>
