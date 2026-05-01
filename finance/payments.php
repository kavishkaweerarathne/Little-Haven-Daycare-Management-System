<?php
session_start();
include '../config.php';

// Auth check
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'finance')) {
    header("Location: ../login/login.php");
    exit();
}

// Fixed Join Query
$query = "SELECT p.*, b.name AS child_name, b.age, b.total_monthly_fee
          FROM payments AS p
          LEFT JOIN billing AS b ON p.billing_id = b.billing_id
          ORDER BY p.payment_id DESC";

$result = mysqli_query($con, $query);

// Total amount
$total_query = "SELECT SUM(amount) as total FROM payments";
$total_result = mysqli_query($con, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: #f5f5f5; min-height: 100vh; }
        
        .sidebar { width: 280px; background: #2c3e50; color: white; position: fixed; height: 100%; padding: 30px; }
        .sidebar h2 { margin-bottom: 30px; font-size: 1.5rem; }
        .nav-links { list-style: none; }
        .nav-links li { margin-bottom: 15px; }
        .nav-links a { color: rgba(255,255,255,0.8); text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 10px; transition: all 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: rgba(255,255,255,0.1); color: white; }
        
        .main-content { margin-left: 280px; padding: 30px; }
        
        .header { background: white; border-radius: 15px; padding: 20px 30px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        /* Summary Card */
        .revenue-card { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 20px; border-radius: 15px; margin-bottom: 30px; display: inline-block; min-width: 300px; }
        .revenue-card h3 { font-size: 0.9rem; opacity: 0.9; }
        .revenue-card p { font-size: 1.8rem; font-weight: bold; }

        /* Table Styling */
        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: #64748b; font-weight: 600; border-bottom: 2px solid #edf2f7; }
        td { padding: 15px; border-bottom: 1px solid #edf2f7; color: #334155; }
        
        .method-badge { background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; color: #475569; border: 1px solid #e2e8f0; }

        @media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fas fa-hands-holding-child"></i> Little Haven</h2>
        <ul class="nav-links">
            <li><a href="finance_dashboard.php"><i class="fas fa-chart-pie"></i> Overview</a></li>
            <li><a href="create_billing.php"><i class="fas fa-plus-circle"></i> Create Billing</a></li>
            <li><a href="invoices.php"><i class="fas fa-file-invoice"></i> Invoices</a></li>
            <li><a href="payments.php" class="active"><i class="fas fa-receipt"></i> Payments</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a></li>
            <li><a href="../admin/admin_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Admin</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Payment History</h1>
            <a href="../login/logout.php" class="logout-btn" style="background: #ef4444; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none;">Logout</a>
        </div>

        <div class="revenue-card">
            <h3><i class="fas fa-wallet"></i> Total Collected</h3>
            <p>Rs. <?php echo number_format($total_row['total'] ?? 0, 2); ?></p>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Child Name</th>
                        <th>Amount Paid</th>
                        <th>Payment Date</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?php echo $row['payment_id']; ?></td>
                        <td><strong><?php echo $row['child_name'] ?? 'N/A'; ?></strong></td>
                        <td style="color: #10b981; font-weight: 600;">Rs. <?php echo number_format($row['amount'], 2); ?></td>
                        <td><i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($row['payment_date'])); ?></td>
                        <td><span class="method-badge"><?php echo $row['payment_method']; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>