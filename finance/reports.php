<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'finance')) {
    header("Location: ../login/login.php");
    exit();
}

// Get summary statistics
$total_revenue_query = "SELECT SUM(amount) as total FROM payments";
$total_revenue_result = mysqli_query($con, $total_revenue_query);
$total_revenue = mysqli_fetch_assoc($total_revenue_result)['total'] ?? 0;

$pending_invoices_query = "SELECT COUNT(*) as pending FROM billing WHERE payment_status = 'pending'";
$pending_invoices_result = mysqli_query($con, $pending_invoices_query);
$pending_invoices = mysqli_fetch_assoc($pending_invoices_result)['pending'] ?? 0;

$total_billed_query = "SELECT SUM(total_monthly_fee) as total FROM billing";
$total_billed_result = mysqli_query($con, $total_billed_query);
$total_billed = mysqli_fetch_assoc($total_billed_result)['total'] ?? 0;

// Get monthly summary
$monthly_summary = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as invoice_count,
                    SUM(total_monthly_fee) as total_amount,
                    SUM(CASE WHEN payment_status = 'paid' THEN total_monthly_fee ELSE 0 END) as collected_amount
                    FROM billing 
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY month DESC
                    LIMIT 6";
$monthly_result = mysqli_query($con, $monthly_summary);

// Get recent payments
$recent_payments = "SELECT p.*, b.name as child_name 
                    FROM payments p 
                    LEFT JOIN billing b ON p.billing_id = b.billing_id 
                    ORDER BY p.payment_date DESC 
                    LIMIT 5";
$recent_result = mysqli_query($con, $recent_payments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Reports | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        }
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
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin-bottom: 20px;
            color: #333;
            border-left: 4px solid #667eea;
            padding-left: 15px;
        }
        .chart-container {
            max-width: 100%;
            height: 300px;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        th { background: #f8f9fa; font-weight: 600; color: #555; }
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
            .row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fas fa-hands-holding-child"></i> Little Haven</h2>
        <ul class="nav-links">
            <li><a href="finance_dashboard.php"><i class="fas fa-chart-pie"></i> Overview</a></li>
            <li><a href="create_billing.php"><i class="fas fa-plus-circle"></i> Create Billing</a></li>
            <li><a href="invoices.php"><i class="fas fa-file-invoice"></i> Invoices</a></li>
            <li><a href="payments.php"><i class="fas fa-receipt"></i> Payments</a></li>
            <li><a href="reports.php" class="active"><i class="fas fa-chart-line"></i> Reports</a></li>
            <li><a href="../admin/admin_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Admin</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Financial Reports</h1>
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
        
        <div class="row">
            <div class="card">
                <h3><i class="fas fa-chart-bar"></i> Monthly Revenue Trend</h3>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-chart-pie"></i> Collection Rate</h3>
                <div class="chart-container">
                    <canvas id="collectionChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h3><i class="fas fa-calendar-alt"></i> Monthly Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Invoices</th>
                        <th>Total Billed (Rs.)</th>
                        <th>Collected (Rs.)</th>
                        <th>Collection Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $months_data = [];
                    $billed_data = [];
                    $collected_data = [];
                    while($row = mysqli_fetch_assoc($monthly_result)): 
                        $months_data[] = date('M Y', strtotime($row['month'] . '-01'));
                        $billed_data[] = $row['total_amount'];
                        $collected_data[] = $row['collected_amount'];
                        $rate = $row['total_amount'] > 0 ? ($row['collected_amount'] / $row['total_amount']) * 100 : 0;
                    ?>
                    <tr>
                        <td><?php echo date('F Y', strtotime($row['month'] . '-01')); ?></td>
                        <td><?php echo $row['invoice_count']; ?></td>
                        <td>Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                        <td>Rs. <?php echo number_format($row['collected_amount'], 2); ?></td>
                        <td><?php echo round($rate, 1); ?>%</td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if(count($months_data) == 0): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No data available</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card">
            <h3><i class="fas fa-clock"></i> Recent Payments</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Child Name</th>
                        <th>Amount (Rs.)</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($recent_result)): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($row['payment_date'])); ?></td>
                        <td><?php echo $row['child_name']; ?></td>
                        <td>Rs. <?php echo number_format($row['amount'], 2); ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $row['payment_method'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if(mysqli_num_rows($recent_result) == 0): ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No recent payments</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
    // Revenue Trend Chart
    const ctx1 = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_reverse($months_data)); ?>,
            datasets: [{
                label: 'Total Billed (Rs.)',
                data: <?php echo json_encode(array_reverse($billed_data)); ?>,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Collected (Rs.)',
                data: <?php echo json_encode(array_reverse($collected_data)); ?>,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
    
    // Collection Rate Chart
    const totalBilled = <?php echo $total_billed; ?>;
    const totalCollected = <?php echo $total_revenue; ?>;
    const pending = totalBilled - totalCollected;
    
    const ctx2 = document.getElementById('collectionChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Collected', 'Pending'],
            datasets: [{
                data: [totalCollected, pending],
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    </script>
</body>
</html>