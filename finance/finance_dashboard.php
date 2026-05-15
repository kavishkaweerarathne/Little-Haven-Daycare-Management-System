<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'finance')) {
    header("Location: ../login/login.php");
    exit();
}

// Create payments table if not exists (safe check)
$create_payments = "CREATE TABLE IF NOT EXISTS `payments` (
    `payment_id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `billing_id` INT(20),
    `child_id` INT(20),
    `amount` DECIMAL(10,2),
    `payment_date` DATE,
    `payment_method` VARCHAR(50),
    `transaction_id` VARCHAR(100),
    `notes` TEXT
)";
mysqli_query($con, $create_payments);

// Create invoices table if not exists
$create_invoices = "CREATE TABLE IF NOT EXISTS `invoices` (
    `invoice_id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `billing_id` INT(20),
    `child_id` INT(20),
    `invoice_number` VARCHAR(50),
    `amount` DECIMAL(10,2),
    `status` VARCHAR(20) DEFAULT 'unpaid',
    `issue_date` DATE,
    `due_date` DATE,
    `paid_date` DATE,
    `payment_method` VARCHAR(50)
)";
mysqli_query($con, $create_invoices);

// Get summary statistics with error handling
$total_revenue = 0;
$revenue_result = mysqli_query($con, "SELECT SUM(amount) as total FROM payments");
if ($revenue_result) {
    $row = mysqli_fetch_assoc($revenue_result);
    $total_revenue = $row['total'] ?? 0;
}

$pending_invoices = 0;
$pending_result = mysqli_query($con, "SELECT COUNT(*) as pending FROM billing WHERE payment_status = 'pending'");
if ($pending_result) {
    $row = mysqli_fetch_assoc($pending_result);
    $pending_invoices = $row['pending'] ?? 0;
}

$total_billed = 0;
$billed_result = mysqli_query($con, "SELECT SUM(total_monthly_fee) as total FROM billing");
if ($billed_result) {
    $row = mysqli_fetch_assoc($billed_result);
    $total_billed = $row['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Dashboard | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }
        .sidebar {
            width: 280px;
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100%;
            padding: 30px;
        }
        .sidebar h2 { margin-bottom: 30px; font-size: 1.5rem; }
        .nav-links { list-style: none; }
        .nav-links li { margin-bottom: 15px; }
        .nav-links a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .nav-links a:hover, .nav-links a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        .header {
            background: white;
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logout-btn {
            background: #ef4444;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .logout-btn:hover { background: #dc2626; }
        .welcome-card {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
        }
        .stat-info h3 { font-size: 0.9rem; color: #666; margin-bottom: 5px; }
        .stat-info p { font-size: 1.8rem; font-weight: bold; color: #333; }
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .action-btn {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .action-btn i { font-size: 2rem; margin-bottom: 10px; display: block; }
        .action-btn h4 { margin-bottom: 5px; }
        .action-btn p { font-size: 0.85rem; color: #666; }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fas fa-hands-holding-child"></i> Little Haven</h2>
        <ul class="nav-links">
            <li><a href="finance_dashboard.php" class="active"><i class="fas fa-chart-pie"></i> Overview</a></li>
            <li><a href="create_billing.php"><i class="fas fa-plus-circle"></i> Create Billing</a></li>
            <li><a href="invoices.php"><i class="fas fa-file-invoice"></i> Invoices</a></li>
            <li><a href="payments.php"><i class="fas fa-receipt"></i> Payments</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Finance Overview</h1>
            <div class="user-info">
                <span>Welcome, <strong><?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Finance Manager'; ?></strong></span>
                <a href="../login/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="welcome-card">
            <h2>Finance Management Portal</h2>
            <p>Monitor revenue, track expenses, and manage parent billings all in one place.</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #10b981;"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-info">
                    <h3>Total Revenue</h3>
                    <p>Rs. <?php echo number_format($total_revenue, 2); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #f59e0b;"><i class="fas fa-file-invoice"></i></div>
                <div class="stat-info">
                    <h3>Pending Invoices</h3>
                    <p><?php echo $pending_invoices; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #ef4444;"><i class="fas fa-chart-line"></i></div>
                <div class="stat-info">
                    <h3>Total Billed</h3>
                    <p>Rs. <?php echo number_format($total_billed, 2); ?></p>
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="create_billing.php" class="action-btn">
                <i class="fas fa-calculator" style="color: #667eea;"></i>
                <h4>Create Billing</h4>
                <p>Generate new child invoice</p>
            </a>
            <a href="invoices.php" class="action-btn">
                <i class="fas fa-file-invoice" style="color: #10b981;"></i>
                <h4>View Invoices</h4>
                <p>Manage all invoices</p>
            </a>
            <a href="payments.php" class="action-btn">
                <i class="fas fa-receipt" style="color: #f59e0b;"></i>
                <h4>Payment History</h4>
                <p>Track all payments</p>
            </a>
            <a href="reports.php" class="action-btn">
                <i class="fas fa-chart-line" style="color: #ef4444;"></i>
                <h4>Reports</h4>
                <p>Financial analysis</p>
            </a>
        </div>
    </div>
</body>
</html>