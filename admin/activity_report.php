<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}

// Fetch all activities for the report
$report_q = "(SELECT 'User Registration' as type, fullname as description, created_at as activity_date, role as meta FROM users)
              UNION
              (SELECT 'Child Enrollment' as type, name as description, enrolled_date as activity_date, 'child' as meta FROM children)
              UNION
              (SELECT 'Daily Update' as type, CONCAT(c.name, ': ', da.mood) as description, da.activity_date as activity_date, 'activity' as meta FROM daily_activities da JOIN children c ON da.child_id = c.id)
              UNION
              (SELECT 'Invoice Issued' as type, CONCAT('Inv #', invoice_number, ' - ', amount) as description, issue_date as activity_date, 'billing' as meta FROM invoices)
              ORDER BY activity_date DESC";
$report_res = mysqli_query($con, $report_q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Activity Report | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #26C6DA;
            --secondary: #1A5276;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 40px;
            color: var(--text-dark);
            background: #fff;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--primary);
            padding-bottom: 20px;
            margin-bottom: 40px;
        }

        .logo-section h1 {
            margin: 0;
            color: var(--secondary);
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .report-info {
            text-align: right;
        }

        .report-info h2 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .report-info p {
            margin: 5px 0 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #f8fafc;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.95rem;
        }

        .type-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            background: #e2e8f0;
            color: #475569;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }

        .no-print-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-family: inherit;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            text-decoration: none;
        }

        .btn-print { background: var(--secondary); color: white; }
        .btn-close { background: #f1f5f9; color: var(--text-dark); }

        @media print {
            .no-print-actions { display: none; }
            body { padding: 0; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>

    <div class="no-print-actions">
        <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Print / Save PDF</button>
        <button onclick="window.close()" class="btn btn-close">Close</button>
    </div>

    <div class="report-header">
        <div class="logo-section">
            <h1><i class="fas fa-hands-holding-child" style="color: var(--primary);"></i> Little Haven</h1>
            <p style="margin: 5px 0 0; color: var(--text-muted); font-size: 0.9rem;">Management System Activity Log</p>
        </div>
        <div class="report-info">
            <h2>Activity Report</h2>
            <p>Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <p>Admin: <?php echo $_SESSION['fullname']; ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Activity Type</th>
                <th>Description</th>
                <th>Module/Role</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($report_res && mysqli_num_rows($report_res) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($report_res)): ?>
                    <tr>
                        <td style="white-space: nowrap; font-weight: 600;">
                            <?php echo date('d M Y', strtotime($row['activity_date'])); ?>
                        </td>
                        <td>
                            <span class="type-badge"><?php echo $row['type']; ?></span>
                        </td>
                        <td>
                            <?php echo $row['description']; ?>
                        </td>
                        <td>
                            <span style="color: var(--text-muted);"><?php echo ucfirst($row['meta']); ?></span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 50px; color: var(--text-muted);">
                        No activities found for the selected period.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>This is a system-generated report from Little Haven Daycare Management System.</p>
        <p>&copy; <?php echo date('Y'); ?> Little Haven. All rights reserved.</p>
    </div>

    <script>
        // Optional: Auto-trigger print after 1 second to allow fonts to load
        window.onload = function() {
            // setTimeout(() => window.print(), 1000);
        }
    </script>
</body>
</html>
