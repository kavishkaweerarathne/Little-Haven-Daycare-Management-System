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
            <?php
            include '../config.php';
            $staff_count_query = "SELECT COUNT(*) as total FROM users WHERE role = 'staff'";
            $staff_count_res = mysqli_query($con, $staff_count_query);
            $staff_count = mysqli_fetch_assoc($staff_count_res)['total'];

            $parent_count_query = "SELECT COUNT(*) as total FROM users WHERE role = 'parent'";
            $parent_count_res = mysqli_query($con, $parent_count_query);
            $parent_count = mysqli_fetch_assoc($parent_count_res)['total'];
            ?>
            <div class="stats-grid">
                <div class="stat-card" onclick="document.querySelector('[data-tab=\'staff\']').click()" style="cursor: pointer;">
                    <div class="stat-icon" style="background: #6366f1;"><i class="fas fa-users"></i></div>
                    <div>
                        <h3 style="margin:0; font-size: 0.9rem; color: #6b7280;">Total Staff</h3>
                        <p style="margin:0; font-size: 1.5rem; font-weight: 700;"><?php echo $staff_count; ?></p>
                    </div>
                </div>
                <div class="stat-card" onclick="document.querySelector('[data-tab=\'parents\']').click()" style="cursor: pointer;">
                    <div class="stat-icon" style="background: #10b981;"><i class="fas fa-user-group"></i></div>
                    <div>
                        <h3 style="margin:0; font-size: 0.9rem; color: #6b7280;">Total Parents</h3>
                        <p style="margin:0; font-size: 1.5rem; font-weight: 700;"><?php echo $parent_count; ?></p>
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
                        <a href="add_user.php?role=staff&tab=staff" class="logout-btn" style="background: var(--primary); text-decoration: none;">+ Add Staff</a>
                    </div>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f3f4f6;">
                            <th style="padding: 1rem;">ID</th>
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
                            echo "<td style='padding: 1rem;'>#".$row['id']."</td>";
                            echo "<td style='padding: 1rem;'>".$row['fullname']."</td>";
                            echo "<td style='padding: 1rem;'>".$row['email']."</td>";
                            echo "<td style='padding: 1rem;'>".$row['phone']."</td>";
                            echo "<td style='padding: 1rem;'>
                                    <a href='view_user.php?id=".$row['id']."&tab=staff' class='action-btn view-btn' style='color: #10b981; margin-right: 10px;'><i class='fas fa-eye'></i></a>
                                    <a href='edit_user.php?id=".$row['id']."&tab=staff' class='action-btn edit-btn'><i class='fas fa-edit'></i></a>
                                    <a href='#' onclick='confirmDelete(".$row['id'].", \"staff\")' class='action-btn delete-btn'><i class='fas fa-trash'></i></a>
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
                    <div style="display: flex; gap: 1rem;">
                        <div style="position: relative;">
                            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                            <input type="text" id="parents-search" placeholder="Search parents..." style="padding: 10px 10px 10px 35px; border: 1px solid #e2e8f0; border-radius: 8px; width: 250px;">
                        </div>
                        <a href="add_user.php?role=parent&tab=parents" class="logout-btn" style="background: var(--primary); text-decoration: none;">+ Add Parent</a>
                    </div>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f3f4f6;">
                            <th style="padding: 1rem;">ID</th>
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
                            echo "<td style='padding: 1rem;'>#".$row['id']."</td>";
                            echo "<td style='padding: 1rem;'>".$row['fullname']."</td>";
                            echo "<td style='padding: 1rem;'>".$row['email']."</td>";
                            echo "<td style='padding: 1rem;'>".$row['phone']."</td>";
                            echo "<td style='padding: 1rem;'>
                                    <a href='view_user.php?id=".$row['id']."&tab=parents' class='action-btn view-btn' style='color: #10b981; margin-right: 10px;'><i class='fas fa-eye'></i></a>
                                    <a href='edit_user.php?id=".$row['id']."&tab=parents' class='action-btn edit-btn'><i class='fas fa-edit'></i></a>
                                    <a href='#' onclick='confirmDelete(".$row['id'].", \"parents\")' class='action-btn delete-btn'><i class='fas fa-trash'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Settings Section -->
        <div id="settings-tab" class="tab-content" style="display: none;">
            <div class="settings-container">
                <div class="settings-sidebar">
                    <div class="settings-nav-item active" data-settings-tab="profile">
                        <i class="fas fa-user-circle"></i>
                        <span>Profile Settings</span>
                    </div>
                    <div class="settings-nav-item" data-settings-tab="system">
                        <i class="fas fa-sliders"></i>
                        <span>System Settings</span>
                    </div>
                    <div class="settings-nav-item" data-settings-tab="security">
                        <i class="fas fa-shield-halved"></i>
                        <span>Security & Privacy</span>
                    </div>
                    <div class="settings-nav-item" data-settings-tab="theme">
                        <i class="fas fa-moon"></i>
                        <span>Theme & Appearance</span>
                    </div>
                    <div class="settings-nav-item" data-settings-tab="help">
                        <i class="fas fa-circle-question"></i>
                        <span>Help & Support</span>
                    </div>
                    <div class="settings-nav-item" data-settings-tab="feedback">
                        <i class="fas fa-comment-dots"></i>
                        <span>Feedback</span>
                    </div>
                    <div class="settings-nav-item logout-nav" data-settings-tab="logout">
                        <i class="fas fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </div>
                </div>

                <div class="settings-main">
                    <!-- Profile Tab -->
                    <div id="settings-profile" class="settings-content active">
                        <div class="settings-header">
                            <h2>Profile Settings</h2>
                            <p>Manage your personal information and how it appears to others.</p>
                        </div>
                        <form class="settings-form">
                            <div class="profile-upload">
                                <div class="avatar-preview">
                                    <img src="https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff" alt="Profile">
                                    <div class="avatar-edit">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>
                                <div class="upload-info">
                                    <h3>Profile Picture</h3>
                                    <p>PNG, JPG or GIF. Max 2MB.</p>
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" value="Administrator" placeholder="Enter full name">
                                </div>
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" value="admin@gmail.com" placeholder="Enter email">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" value="+94 77 123 4567" placeholder="Enter phone">
                                </div>
                                <div class="form-group">
                                    <label>Position</label>
                                    <input type="text" value="Head Administrator" readonly>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="save-btn">Save Changes</button>
                                <button type="reset" class="cancel-btn">Reset</button>
                            </div>
                        </form>
                    </div>

                    <!-- System Tab -->
                    <div id="settings-system" class="settings-content" style="display: none;">
                        <div class="settings-header">
                            <h2>System Settings</h2>
                            <p>Configure daycare wide settings and preferences.</p>
                        </div>
                        <form class="settings-form">
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label>Daycare Name</label>
                                    <input type="text" value="Little Haven Daycare">
                                </div>
                                <div class="form-group">
                                    <label>Primary Contact Email</label>
                                    <input type="email" value="info@littlehaven.com">
                                </div>
                                <div class="form-group">
                                    <label>Primary Phone</label>
                                    <input type="tel" value="+94 11 234 5678">
                                </div>
                                <div class="form-group">
                                    <label>Opening Time</label>
                                    <input type="time" value="07:30">
                                </div>
                                <div class="form-group">
                                    <label>Closing Time</label>
                                    <input type="time" value="18:30">
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="save-btn">Update System</button>
                            </div>
                        </form>
                    </div>

                    <!-- Security Tab -->
                    <div id="settings-security" class="settings-content" style="display: none;">
                        <div class="settings-header">
                            <h2>Security & Privacy</h2>
                            <p>Update your password and manage security preferences.</p>
                        </div>
                        <form class="settings-form">
                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" placeholder="••••••••">
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" placeholder="••••••••">
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" placeholder="••••••••">
                            </div>
                            <div class="security-list">
                                <div class="security-item">
                                    <div class="item-info">
                                        <h4>Two-Factor Authentication</h4>
                                        <p>Add an extra layer of security to your account.</p>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="security-item">
                                    <div class="item-info">
                                        <h4>Session Timeout</h4>
                                        <p>Automatically log out after 30 minutes of inactivity.</p>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="save-btn">Change Password</button>
                            </div>
                        </form>
                    </div>

                    <!-- Theme Tab -->
                    <div id="settings-theme" class="settings-content" style="display: none;">
                        <div class="settings-header">
                            <h2>Theme & Appearance</h2>
                            <p>Customize how the dashboard looks and feels.</p>
                        </div>
                        <div class="theme-options">
                            <div class="theme-card active" data-theme="light">
                                <div class="theme-preview light">
                                    <div class="preview-sidebar"></div>
                                    <div class="preview-content">
                                        <div class="preview-line"></div>
                                        <div class="preview-line"></div>
                                    </div>
                                </div>
                                <h4>Light Mode</h4>
                                <p>Clean and bright interface</p>
                            </div>
                            <div class="theme-card" data-theme="dark">
                                <div class="theme-preview dark">
                                    <div class="preview-sidebar"></div>
                                    <div class="preview-content">
                                        <div class="preview-line"></div>
                                        <div class="preview-line"></div>
                                    </div>
                                </div>
                                <h4>Dark Mode</h4>
                                <p>Easier on the eyes in low light</p>
                            </div>
                        </div>
                    </div>

                    <!-- Help Tab -->
                    <div id="settings-help" class="settings-content" style="display: none;">
                        <div class="settings-header">
                            <h2>Help & Support</h2>
                            <p>Find answers to common questions or contact support.</p>
                        </div>
                        <div class="faq-list">
                            <div class="faq-item">
                                <h4>How do I add a new staff member?</h4>
                                <p>Go to the "Manage Staff" tab and click the "+ Add Staff" button.</p>
                            </div>
                            <div class="faq-item">
                                <h4>Can I change the system language?</h4>
                                <p>Currently, the system only supports English. More languages coming soon.</p>
                            </div>
                            <div class="faq-item">
                                <h4>How to reset a parent's password?</h4>
                                <p>Go to "Manage Parents", edit the user, and use the "Reset Password" option.</p>
                            </div>
                        </div>
                        <div class="contact-support">
                            <h3>Still need help?</h3>
                            <p>Our support team is available 24/7.</p>
                            <a href="mailto:support@littlehaven.com" class="save-btn" style="text-decoration: none; display: inline-block;">Contact Support</a>
                        </div>
                    </div>

                    <!-- Feedback Tab -->
                    <div id="settings-feedback" class="settings-content" style="display: none;">
                        <div class="settings-header">
                            <h2>Share your feedback</h2>
                            <p>Help us improve Little Haven Management System.</p>
                        </div>
                        <form class="settings-form feedback-form">
                            <div class="form-group">
                                <label>How would you rate your experience?</label>
                                <div class="rating-stars">
                                    <i class="far fa-star" data-rating="1"></i>
                                    <i class="far fa-star" data-rating="2"></i>
                                    <i class="far fa-star" data-rating="3"></i>
                                    <i class="far fa-star" data-rating="4"></i>
                                    <i class="far fa-star" data-rating="5"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>What can we improve?</label>
                                <textarea placeholder="Tell us what you think..." rows="5" style="padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; resize: vertical;"></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="save-btn">Submit Feedback</button>
                            </div>
                        </form>
                    </div>

                    <!-- Logout Tab -->
                    <div id="settings-logout" class="settings-content" style="display: none;">
                        <div class="settings-header">
                            <h2>Logout</h2>
                            <p>Are you sure you want to end your session?</p>
                        </div>
                        <div class="logout-confirm-card">
                            <i class="fas fa-right-from-bracket logout-icon"></i>
                            <h3>You are about to log out</h3>
                            <p>Any unsaved changes will be lost.</p>
                            <div class="form-actions" style="justify-content: center; border: none;">
                                <a href="../login/logout.php" class="logout-btn" style="padding: 12px 40px;">Yes, Logout</a>
                                <button type="button" class="cancel-btn" onclick="document.querySelector('[data-settings-tab=\'profile\']').click()">Stay Logged In</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Custom JS -->
    <script src="admin_dashboard.js"></script>
</body>
</html>
