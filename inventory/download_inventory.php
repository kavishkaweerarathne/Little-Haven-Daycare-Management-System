<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

$inventory = $con->query("SELECT * FROM inventory ORDER BY category ASC, item_name ASC");
$date = date('d M Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Summary - Little Haven</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; color: #333; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #FF9F1C; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #FF9F1C; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; font-size: 0.8rem; color: #888; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
        .btn-print {
            background: #264653;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <a href="inventory_dashboard.php" style="color: #666; text-decoration: none;">← Back to Dashboard</a>
        <br><br>
        <button onclick="window.print()" class="btn-print">Print / Save as PDF</button>
    </div>

    <div class="header">
        <h1>Little Haven Daycare</h1>
        <h2>Inventory Summary Report</h2>
        <p>Generated on: <?php echo $date; ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Current Stock</th>
                <th>Supplier</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = $inventory->fetch_assoc()): ?>
            <tr>
                <td><strong><?php echo $item['item_name']; ?></strong></td>
                <td><?php echo $item['category']; ?></td>
                <td><?php echo $item['quantity'] . " " . $item['unit']; ?></td>
                <td><?php echo $item['supplier_name'] ?: '-'; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Little Haven Daycare Management System. All rights reserved.</p>
    </div>

    <script>
        // Optional: Auto-trigger print
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
