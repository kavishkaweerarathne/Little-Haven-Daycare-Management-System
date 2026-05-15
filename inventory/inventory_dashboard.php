<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'inventory';

// Fetch stats
$total_items = $con->query("SELECT COUNT(*) as count FROM inventory")->fetch_assoc()['count'];
$low_stock = $con->query("SELECT COUNT(*) as count FROM inventory WHERE quantity <= 5 AND quantity > 0")->fetch_assoc()['count'];
$out_of_stock = $con->query("SELECT COUNT(*) as count FROM inventory WHERE quantity <= 0")->fetch_assoc()['count'];

// Data for different tabs
if ($tab == 'inventory') {
    $inventory_result = $con->query("SELECT * FROM inventory ORDER BY id DESC");
} elseif ($tab == 'suppliers') {
    $suppliers_result = $con->query("SELECT * FROM suppliers ORDER BY name ASC");
} elseif ($tab == 'orders') {
    $orders_result = $con->query("SELECT o.*, s.name as supplier_name FROM inventory_orders o LEFT JOIN suppliers s ON o.supplier_id = s.id ORDER BY o.order_date DESC");
} elseif ($tab == 'stock_level') {
    $low_stock_items = $con->query("SELECT * FROM inventory WHERE quantity <= 5 ORDER BY quantity ASC");
} elseif ($tab == 'settings') {
    $user_id = $_SESSION['user_id'];
    
    // Handle Profile Update
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
        $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $phone = mysqli_real_escape_string($con, $_POST['phone']);
        
        if (empty($fullname) || empty($email)) {
            $error = 'Full name and email are required.';
        } elseif (!empty($phone) && !preg_match("/^[0-9]{10}$/", $phone)) {
            $error = 'Phone number must be exactly 10 digits.';
        } else {
            // Check if email is already taken by another user
            $check_email = $con->query("SELECT id FROM users WHERE email = '$email' AND id != $user_id");
            if ($check_email->num_rows > 0) {
                $error = 'Email address is already in use by another account.';
            } else {
                $stmt = $con->prepare("UPDATE users SET fullname = ?, email = ?, phone = ? WHERE id = ?");
                $stmt->bind_param("sssi", $fullname, $email, $phone, $user_id);
                if ($stmt->execute()) {
                    $_SESSION['fullname'] = $fullname;
                    $_SESSION['email'] = $email;
                    $success = 'Profile updated successfully!';
                } else {
                    $error = 'Error updating profile.';
                }
            }
        }
    }
    
    // Handle Password Update
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_security'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        $user = $con->query("SELECT password FROM users WHERE id = $user_id")->fetch_assoc();
        
        if ($current_password !== $user['password']) {
            $error = 'Current password is incorrect.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match.';
        } elseif (strlen($new_password) < 4) {
            $error = 'New password must be at least 4 characters.';
        } else {
            $stmt = $con->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_password, $user_id);
            if ($stmt->execute()) {
                $success = 'Password updated successfully!';
            } else {
                $error = 'Error updating password.';
            }
        }
    }

    $user_data = $con->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard | Little Haven</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #26C6DA;
            --primary-dark: #00ACC1;
            --secondary: #1A5276;
            --bg-alt: #F7FAFC;
            --text-main: #1A202C;
            --text-muted: #718096;
            --radius-md: 20px;
            --radius-sm: 12px;
            --danger: #ef4444;
            --success: #22c55e;
            --warning: #f59e0b;
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
            position: fixed;
            height: 100vh;
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
            margin-left: 280px;
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
            box-shadow: 0 15px 35px rgba(38, 70, 83, 0.2);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
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

        /* Inventory Management Styles */
        .inventory-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 20px;
        }

        .search-box {
            background: white;
            padding: 12px 20px;
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            max-width: 400px;
        }

        .search-box input {
            border: none;
            outline: none;
            width: 100%;
            font-family: inherit;
            font-size: 1rem;
        }

        .add-btn {
            background: var(--secondary);
            color: white;
            padding: 12px 25px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .add-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            color: white;
        }

        .table-container {
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: #f8fafc;
            padding: 18px 25px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }

        td {
            padding: 18px 25px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-category {
            background: #EBF8FF;
            color: #3182CE;
        }

        .stock-ok {
            color: var(--success);
            font-weight: 600;
        }

        .stock-low {
            color: var(--danger);
            font-weight: 600;
        }

        .status-pending { background: #FEF3C7; color: #D97706; }
        .status-received { background: #D1FAE5; color: #059669; }
        .status-cancelled { background: #FEE2E2; color: #DC2626; }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .edit-btn { background: #EBF8FF; color: #3182CE; }
        .edit-btn:hover { background: #3182CE; color: white; }

        .delete-btn:hover { background: var(--danger); color: white; }

        .logout-btn {
            background: #ef4444;
            color: white !important;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .logout-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(239, 68, 68, 0.3);
        }
        /* Settings UI */
        .settings-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 40px;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            min-height: 500px;
        }

        .settings-sidebar {
            border-right: 1.5px solid #F1F5F9;
            padding-right: 20px;
        }

        .settings-tab {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            border-radius: 12px;
            color: #64748B;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            margin-bottom: 10px;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-family: inherit;
        }

        .settings-tab.active {
            background: #E0F7FA;
            color: #00838F;
        }

        .settings-tab i { font-size: 1.1rem; }

        .settings-content { padding: 10px 0; }
        .settings-section { display: none; }
        .settings-section.active { display: block; }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            background: #B2EBF2;
            color: #00838F;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            border-radius: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .input-group label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #475569;
        }

        .input-field {
            padding: 12px 16px;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: #F8FAFC;
        }

        .input-field:focus {
            border-color: #26C6DA;
            background: white;
            box-shadow: 0 0 0 4px rgba(38, 198, 218, 0.1);
        }

        .input-field:disabled {
            background: #F1F5F9;
            cursor: not-allowed;
            color: #94A3B8;
        }

        .save-btn {
            background: #26C6DA;
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .save-btn:hover { background: #00ACC1; transform: translateY(-2px); }
    </style>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fas fa-hands-holding-child"></i> Little Haven</h2>
        <div class="nav-links">
            <a href="inventory_dashboard.php?tab=inventory" class="nav-item <?php echo $tab == 'inventory' ? 'active' : ''; ?>"><i class="fas fa-boxes-stacked"></i> Overview</a>
            <a href="inventory_dashboard.php?tab=orders" class="nav-item <?php echo $tab == 'orders' ? 'active' : ''; ?>"><i class="fas fa-cart-plus"></i> Orders</a>
            <a href="inventory_dashboard.php?tab=suppliers" class="nav-item <?php echo $tab == 'suppliers' ? 'active' : ''; ?>"><i class="fas fa-truck-ramp-box"></i> Suppliers</a>
            <a href="inventory_dashboard.php?tab=stock_level" class="nav-item <?php echo $tab == 'stock_level' ? 'active' : ''; ?>"><i class="fas fa-warehouse"></i> Stock Level</a>
            <a href="inventory_dashboard.php?tab=settings" class="nav-item <?php echo $tab == 'settings' ? 'active' : ''; ?>"><i class="fas fa-gear"></i> Settings</a>
        </div>
    </div>

    <main class="main-content">
        <div class="header">
            <h1><?php echo $tab == 'inventory' ? 'Inventory Overview' : ucfirst(str_replace('_', ' ', $tab)); ?></h1>
            <div class="user-info">
                <span>Welcome, <strong><?php echo $_SESSION['fullname']; ?></strong></span>
                <a href="../login/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <?php if ($tab == 'inventory'): ?>
            <div class="welcome-card">
                <h2>Inventory Control Center</h2>
                <p>Manage daycare supplies, track equipment, and monitor stock levels efficiently.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #FF9F1C;"><i class="fas fa-box-open"></i></div>
                    <div>
                        <h3 style="font-size: 0.9rem; color: var(--text-muted);">Total Items</h3>
                        <p style="font-size: 1.5rem; font-weight: 700;"><?php echo $total_items; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #E76F51;"><i class="fas fa-triangle-exclamation"></i></div>
                    <div>
                        <h3 style="font-size: 0.9rem; color: var(--text-muted);">Low Stock Alerts</h3>
                        <p style="font-size: 1.5rem; font-weight: 700;"><?php echo $low_stock; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #2A9D8F;"><i class="fas fa-circle-xmark"></i></div>
                    <div>
                        <h3 style="font-size: 0.9rem; color: var(--text-muted);">Out of Stock</h3>
                        <p style="font-size: 1.5rem; font-weight: 700;"><?php echo $out_of_stock; ?></p>
                    </div>
                </div>
            </div>

            <div class="inventory-controls">
                <div class="search-box">
                    <i class="fas fa-search text-muted"></i>
                    <input type="text" id="inventorySearch" placeholder="Search inventory items...">
                </div>
                <a href="download_inventory.php" class="add-btn" style="background: #E2E8F0; color: var(--text-main);">
                    <i class="fas fa-file-pdf"></i> Download Inventory List
                </a>
            </div>

            <div class="table-container">
                <table id="inventoryTable">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Stock Level</th>
                            <th>Supplier</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($inventory_result->num_rows > 0): ?>
                            <?php while ($item = $inventory_result->fetch_assoc()): ?>
                                <?php $isLowStock = ($item['quantity'] <= 5 && $item['quantity'] > 0); ?>
                                <?php $isOutOfStock = ($item['quantity'] <= 0); ?>
                                <tr>
                                    <td><?php echo $item['item_name']; ?></td>
                                    <td><span class="badge badge-category"><?php echo $item['category'] ?: 'N/A'; ?></span></td>
                                    <td>
                                        <span class="<?php echo ($isLowStock || $isOutOfStock) ? 'stock-low' : 'stock-ok'; ?>">
                                            <?php echo $item['quantity'] . " " . $item['unit']; ?>
                                        </span>
                                        <?php if ($isLowStock): ?>
                                            <i class="fas fa-exclamation-triangle text-warning" title="Low Stock"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $item['supplier_name'] ?: '-'; ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="edit_item.php?id=<?php echo $item['id']; ?>" class="btn-icon edit-btn"><i class="fas fa-edit"></i></a>
                                            <a href="javascript:void(0)" onclick="confirmDeleteItem(<?php echo $item['id']; ?>)" class="btn-icon delete-btn"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center; padding: 40px;">No items found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($tab == 'orders'): ?>
            <div class="inventory-controls">
                <div class="search-box">
                    <i class="fas fa-search text-muted"></i>
                    <input type="text" id="orderSearch" placeholder="Search orders...">
                </div>
                <a href="create_order.php" class="add-btn">
                    <i class="fas fa-cart-plus"></i> Create New Order
                </a>
            </div>

            <div class="table-container">
                <table id="orderTable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Item Name</th>
                            <th>Supplier</th>
                            <th>Quantity</th>
                            <th>Total Cost</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orders_result->num_rows > 0): ?>
                            <?php while ($order = $orders_result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo $order['item_name']; ?></td>
                                    <td><?php echo $order['supplier_name'] ?: 'N/A'; ?></td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td><strong>Rs. <?php echo number_format($order['total_cost'], 2); ?></strong></td>
                                    <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                    <td><span class="badge status-<?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></span></td>
                                    <td>
                                        <div class="actions">
                                            <a href="update_order_status.php?id=<?php echo $order['id']; ?>&status=Received" class="btn-icon edit-btn" title="Mark as Received"><i class="fas fa-check"></i></a>
                                            <a href="update_order_status.php?id=<?php echo $order['id']; ?>&status=Cancelled" class="btn-icon delete-btn" title="Cancel Order"><i class="fas fa-times"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" style="text-align: center; padding: 40px;">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($tab == 'suppliers'): ?>
            <div class="inventory-controls">
                <div class="search-box">
                    <i class="fas fa-search text-muted"></i>
                    <input type="text" id="supplierSearch" placeholder="Search suppliers...">
                </div>
                <a href="add_supplier.php" class="add-btn">
                    <i class="fas fa-plus"></i> Add New Supplier
                </a>
            </div>

            <div class="table-container">
                <table id="supplierTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($suppliers_result->num_rows > 0): ?>
                            <?php while ($supplier = $suppliers_result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo $supplier['name']; ?></strong></td>
                                    <td><?php echo $supplier['phone'] ?: '-'; ?></td>
                                    <td><?php echo $supplier['email'] ?: '-'; ?></td>
                                    <td><span class="badge badge-category"><?php echo $supplier['category'] ?: 'General'; ?></span></td>
                                    <td>
                                        <div class="actions">
                                            <a href="edit_supplier.php?id=<?php echo $supplier['id']; ?>" class="btn-icon edit-btn"><i class="fas fa-edit"></i></a>
                                            <a href="javascript:void(0)" onclick="confirmDeleteSupplier(<?php echo $supplier['id']; ?>)" class="btn-icon delete-btn"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center; padding: 40px;">No suppliers found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($tab == 'stock_level'): ?>
            <div class="welcome-card" style="background: linear-gradient(135deg, var(--danger), var(--primary-dark));">
                <h2>Critical Stock Levels</h2>
                <p>Items listed here are below reorder threshold or completely out of stock.</p>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Current Stock</th>
                            <th>Min. Threshold</th>
                            <th>Gap</th>
                            <th>Action Needed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($low_stock_items->num_rows > 0): ?>
                            <?php while ($item = $low_stock_items->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo $item['item_name']; ?></strong></td>
                                    <td><span class="stock-low"><?php echo $item['quantity'] . " " . $item['unit']; ?></span></td>
                                    <td>5</td>
                                    <td><span style="color: var(--danger); font-weight: bold;"><?php echo 5 - $item['quantity']; ?> needed</span></td>
                                    <td><a href="create_order.php?item=<?php echo urlencode($item['item_name']); ?>" class="add-btn" style="padding: 8px 15px; font-size: 0.8rem;">Order Now</a></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align: center; padding: 40px;">All stock levels are optimal.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($tab == 'settings'): ?>
            <div class="settings-container">
                <!-- Settings Sidebar -->
                <div class="settings-sidebar">
                    <button class="settings-tab active" onclick="switchSettingsTab('profile')">
                        <i class="fas fa-user-circle"></i> Profile
                    </button>
                    <button class="settings-tab" onclick="switchSettingsTab('security')">
                        <i class="fas fa-shield-halved"></i> Security
                    </button>
                </div>

                <!-- Settings Content -->
                <div class="settings-content">
                    <!-- Profile Section -->
                    <div id="profile-section" class="settings-section active">
                        <div class="profile-header">
                            <div class="avatar-circle">
                                <?php echo strtoupper(substr($user_data['fullname'], 0, 1)); ?>
                            </div>
                            <div>
                                <h2 style="margin: 0; font-size: 1.5rem;">My Profile</h2>
                                <p style="margin: 5px 0 0; color: #64748B;">Manage your personal information and contact details.</p>
                            </div>
                        </div>

                        <form method="POST" action="inventory_dashboard.php?tab=settings">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="form-row">
                                <div class="input-group">
                                    <label><i class="fas fa-user"></i> Full Name</label>
                                    <input type="text" name="fullname" class="input-field" value="<?php echo $user_data['fullname']; ?>" required>
                                </div>
                                <div class="input-group">
                                    <label><i class="fas fa-envelope"></i> Email Address</label>
                                    <input type="email" name="email" class="input-field" value="<?php echo $user_data['email']; ?>" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="input-group">
                                    <label><i class="fas fa-phone"></i> Phone Number</label>
                                    <input type="text" name="phone" id="settings_phone" class="input-field" value="<?php echo $user_data['phone'] ?? ''; ?>" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');" placeholder="e.g. 0712345678">
                                </div>
                                <div class="input-group">
                                    <label><i class="fas fa-user-shield"></i> Account Role</label>
                                    <input type="text" class="input-field" value="Inventory Manager" disabled>
                                </div>
                            </div>
                            <button type="submit" class="save-btn">
                                <i class="fas fa-save"></i> Save Profile Changes
                            </button>
                        </form>
                    </div>

                    <!-- Security Section -->
                    <div id="security-section" class="settings-section">
                        <div class="profile-header">
                            <div class="avatar-circle" style="background: #FEF3C7; color: #D97706;">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div>
                                <h2 style="margin: 0; font-size: 1.5rem;">Password & Security</h2>
                                <p style="margin: 5px 0 0; color: #64748B;">Update your password to keep your account secure.</p>
                            </div>
                        </div>

                        <form method="POST" action="inventory_dashboard.php?tab=settings" style="max-width: 500px;">
                            <input type="hidden" name="update_security" value="1">
                            <div class="input-group" style="margin-bottom: 20px;">
                                <label>Current Password</label>
                                <input type="password" name="current_password" class="input-field" required placeholder="Enter current password">
                            </div>
                            <div class="input-group" style="margin-bottom: 20px;">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="input-field" required minlength="6" placeholder="Enter new password">
                            </div>
                            <div class="input-group" style="margin-bottom: 25px;">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" class="input-field" required minlength="6" placeholder="Repeat new password">
                            </div>
                            <button type="submit" class="save-btn" style="background: #059669;">
                                <i class="fas fa-shield-check"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script>
        // Tab Switching for Settings
        function switchSettingsTab(tabName) {
            // Update tabs
            document.querySelectorAll('.settings-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.currentTarget.classList.add('active');

            // Update sections
            document.querySelectorAll('.settings-section').forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(tabName + '-section').classList.add('active');
        }

        // SweetAlert Notifications
        <?php if(isset($success)): ?>
            Swal.fire('Success', '<?php echo $success; ?>', 'success');
        <?php endif; ?>

        <?php if(isset($error)): ?>
            Swal.fire('Error', '<?php echo $error; ?>', 'error');
        <?php endif; ?>
        function confirmDeleteItem(id) {
            if (confirm('Are you sure you want to delete this item? It will be moved to the archive.')) {
                window.location.href = 'delete_item.php?id=' + id;
            }
        }

        function confirmDeleteSupplier(id) {
            if (confirm('Are you sure you want to delete this supplier?')) {
                window.location.href = 'delete_supplier.php?id=' + id;
            }
        }

        // Simple Search Filtering
        function initSearch(inputId, tableId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            input.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        }

        initSearch('inventorySearch', 'inventoryTable');
        initSearch('orderSearch', 'orderTable');
        initSearch('supplierSearch', 'supplierTable');
    </script>
</body>
</html>
