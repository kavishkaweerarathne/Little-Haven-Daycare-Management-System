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
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #10b981;
            --bg: #f3f4f6;
            --text: #1f2937;
            --sidebar: #1e1b4b;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 280px;
            background: var(--sidebar);
            color: white;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar nav p {
            padding: 12px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            margin: 5px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar nav p:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar nav p.active {
            background: var(--primary);
        }
        .main-content {
            flex: 1;
            padding: 2.5rem;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        .logout-btn {
            background: #ef4444;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.3s;
        }
        .logout-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fas fa-hands-holding-child"></i> Little Haven</h2>
        <nav>
            <p class="active"><i class="fas fa-chart-line"></i> Dashboard</p>
            <p><i class="fas fa-users"></i> Manage Staff</p>
            <p><i class="fas fa-user-group"></i> Manage Parents</p>
            <p><i class="fas fa-baby"></i> Manage Children</p>
            <p><i class="fas fa-file-invoice-dollar"></i> Finance</p>
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
</body>
</html>
