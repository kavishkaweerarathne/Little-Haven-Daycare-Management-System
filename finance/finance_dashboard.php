<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'finance') {
    header("Location: ../login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Dashboard | Little Haven</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #26C6DA;
            --primary-dark: #0097A7;
            --secondary: #1A5276;
            --bg-alt: #F7FAFC;
            --text-main: #1A202C;
            --text-muted: #718096;
            --radius-md: 20px;
            --shadow-soft: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-alt);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: var(--secondary);
            color: white;
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .nav-item {
            padding: 12px 18px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.7);
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .main-content {
            flex: 1;
            padding: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-btn {
            background: white;
            color: var(--secondary);
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
        }

        .welcome-card {
            background: linear-gradient(135deg, var(--secondary), var(--primary-dark));
            color: white;
            padding: 40px;
            border-radius: var(--radius-md);
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(26, 82, 118, 0.2);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .placeholder-section {
            margin-top: 40px;
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: var(--radius-md);
            border: 2px dashed #e2e8f0;
        }

        .placeholder-section i {
            font-size: 4rem;
            color: var(--primary);
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fas fa-hands-holding-child"></i> Little Haven</h2>
        <div class="nav-links">
            <a href="#" class="nav-item active"><i class="fas fa-chart-pie"></i> Overview</a>
            <a href="#" class="nav-item"><i class="fas fa-file-invoice-dollar"></i> Invoices</a>
            <a href="#" class="nav-item"><i class="fas fa-receipt"></i> Payments</a>
            <a href="#" class="nav-item"><i class="fas fa-wallet"></i> Expenses</a>
            <a href="#" class="nav-item"><i class="fas fa-chart-line"></i> Reports</a>
            <a href="#" class="nav-item"><i class="fas fa-gear"></i> Settings</a>
        </div>
    </div>

    <main class="main-content">
        <div class="header">
            <h1>Finance Overview</h1>
            <div class="user-info">
                <span>Welcome, <strong><?php echo $_SESSION['fullname']; ?></strong></span>
                <a href="../login/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <div class="welcome-card">
            <h2>Finance Management Portal</h2>
            <p>Monitor revenue, track expenses, and manage parent billings all in one place.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #10B981;"><i class="fas fa-money-bill-trend-up"></i></div>
                <div>
                    <h3 style="font-size: 0.9rem; color: var(--text-muted);">Total Revenue</h3>
                    <p style="font-size: 1.5rem; font-weight: 700;">$0.00</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #F59E0B;"><i class="fas fa-file-invoice"></i></div>
                <div>
                    <h3 style="font-size: 0.9rem; color: var(--text-muted);">Pending Invoices</h3>
                    <p style="font-size: 1.5rem; font-weight: 700;">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #EF4444;"><i class="fas fa-wallet"></i></div>
                <div>
                    <h3 style="font-size: 0.9rem; color: var(--text-muted);">Total Expenses</h3>
                    <p style="font-size: 1.5rem; font-weight: 700;">$0.00</p>
                </div>
            </div>
        </div>

        <div class="placeholder-section">
            <i class="fas fa-toolbox"></i>
            <h3>Financial Tools Coming Soon</h3>
            <p>We are currently setting up your advanced financial management modules. Check back soon for full functionality!</p>
        </div>
    </main>

</body>
</html>
