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
            --primary: #FF9F1C;
            --primary-dark: #E76F51;
            --secondary: #264653;
            --bg-alt: #F7FAFC;
            --text-main: #1A202C;
            --text-muted: #718096;
            --radius-md: 20px;
            --radius-sm: 12px;
            --shadow-soft: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            --danger: #E63946;
            --success: #2A9D8F;
            --warning: #FF9F1C;
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

        .delete-btn { background: #FFF5F5; color: var(--danger); }
        .delete-btn:hover { background: var(--danger); color: white; }
    </style>
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
            <div class="table-container" style="padding: 40px; max-width: 600px;">
                <h2 style="margin-bottom: 20px;">Manager Settings</h2>
                <form action="update_settings.php" method="POST">
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 600;">Full Name</label>
                            <input type="text" name="fullname" value="<?php echo $_SESSION['fullname']; ?>" required style="padding: 12px; border: 1.5px solid #E2E8F0; border-radius: 10px;">
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 600;">Email Address</label>
                            <input type="email" value="<?php echo $_SESSION['email']; ?>" disabled style="padding: 12px; border: 1.5px solid #E2E8F0; border-radius: 10px; background: #f8fafc;">
                        </div>
                        
                        <hr style="border: 0; border-top: 1px solid #edf2f7; margin: 10px 0;">
                        <h4 style="color: var(--text-muted);">Change Password (Optional)</h4>

                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 600;">Current Password</label>
                            <input type="password" name="current_password" placeholder="Required to change password" style="padding: 12px; border: 1.5px solid #E2E8F0; border-radius: 10px;">
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 600;">New Password</label>
                            <input type="password" name="new_password" placeholder="Enter new password" style="padding: 12px; border: 1.5px solid #E2E8F0; border-radius: 10px;">
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 600;">Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="Repeat new password" style="padding: 12px; border: 1.5px solid #E2E8F0; border-radius: 10px;">
                        </div>
                        
                        <button type="submit" class="add-btn" style="width: 100%; justify-content: center; margin-top: 10px;">Save Changes</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>

    <script>
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
