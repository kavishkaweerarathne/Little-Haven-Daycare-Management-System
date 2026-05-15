<?php
session_start();
include '../config.php';

// Auth check
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'finance')) {
    header("Location: ../login/login.php");
    exit();
}

// Fetch Invoices
$query = "SELECT b.* FROM billing AS b ORDER BY b.billing_id DESC";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: #f5f5f5; min-height: 100vh; }
        
        /* Sidebar & Layout */
        .sidebar { width: 280px; background: #2c3e50; color: white; position: fixed; height: 100%; padding: 30px; }
        .sidebar h2 { margin-bottom: 30px; font-size: 1.5rem; }
        .nav-links { list-style: none; }
        .nav-links li { margin-bottom: 15px; }
        .nav-links a { color: rgba(255,255,255,0.8); text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 10px; transition: all 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: rgba(255,255,255,0.1); color: white; }
        
        .main-content { margin-left: 280px; padding: 30px; }
        
        /* Header Card */
        .header { background: white; border-radius: 15px; padding: 20px 30px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .logout-btn { background: #ef4444; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; transition: 0.3s; }
        
        /* Table Styling */
        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow-x: auto; }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: #64748b; font-weight: 600; border-bottom: 2px solid #edf2f7; }
        td { padding: 15px; border-bottom: 1px solid #edf2f7; color: #334155; }
        tr:hover { background-color: #fcfcfc; }

        /* Status Badges */
        .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; text-transform: capitalize; }
        .status-paid { background: #dcfce7; color: #15803d; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        
        .pay-btn { background: #3498db; color: white; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 0.9rem; transition: 0.3s; }
        .pay-btn:hover { background: #2980b9; }

        @media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fas fa-hands-holding-child"></i> Little Haven</h2>
        <ul class="nav-links">
            <li><a href="finance_dashboard.php"><i class="fas fa-chart-pie"></i> Overview</a></li>
            <li><a href="create_billing.php"><i class="fas fa-plus-circle"></i> Create Billing</a></li>
            <li><a href="invoices.php" class="active"><i class="fas fa-file-invoice"></i> Invoices</a></li>
            <li><a href="payments.php"><i class="fas fa-receipt"></i> Payments</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Manage Invoices</h1>
            <div class="user-info">
                <a href="../login/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="table-container">
            <div class="table-header">
                <h3>Billing List</h3>
                <a href="create_billing.php" style="text-decoration:none; color:#3498db;"><i class="fas fa-plus"></i> New Billing</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Billing ID</th>
                        <th>Child Name</th>
                        <th>Age</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?php echo $row['billing_id']; ?></td>
                        <td><strong><?php echo $row['name']; ?></strong></td>
                        <td><?php echo $row['age']; ?> Yrs</td>
                        <td>Rs. <?php echo number_format($row['total_monthly_fee'], 2); ?></td>
                        <td>
                            <span class="status-badge <?php echo ($row['payment_status'] == 'paid') ? 'status-paid' : 'status-pending'; ?>">
                                <?php echo $row['payment_status']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="process_payment.php?id=<?php echo $row['billing_id']; ?>" class="pay-btn">
                                <i class="fas fa-credit-card"></i> Pay
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>