<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = mysqli_real_escape_string($con, $_GET['status']);
    
    // Begin transaction
    $con->begin_transaction();
    try {
        if ($con->query("UPDATE inventory_orders SET status = '$status' WHERE id = $id")) {
            // If status is Received, update inventory
            if ($status === 'Received') {
                $order_res = $con->query("SELECT * FROM inventory_orders WHERE id = $id");
                $order = $order_res->fetch_assoc();
                
                $item_name = $order['item_name'];
                $quantity = $order['quantity'];
                
                // Check if item exists in inventory
                $check_res = $con->query("SELECT id, quantity FROM inventory WHERE item_name = '$item_name'");
                if ($check_res && $check_res->num_rows > 0) {
                    $inv_item = $check_res->fetch_assoc();
                    $new_qty = $inv_item['quantity'] + $quantity;
                    $con->query("UPDATE inventory SET quantity = $new_qty WHERE id = " . $inv_item['id']);
                } else {
                    // Fetch supplier name if available
                    $supplier_id = $order['supplier_id'];
                    $s_name = '-';
                    if ($supplier_id) {
                        $s_res = $con->query("SELECT name FROM suppliers WHERE id = $supplier_id");
                        if ($s_res && $s_res->num_rows > 0) {
                            $s_name = $s_res->fetch_assoc()['name'];
                        }
                    }
                    // Insert new item
                    $con->query("INSERT INTO inventory (item_name, quantity, unit, supplier_name) VALUES ('$item_name', $quantity, 'Units', '$s_name')");
                }
            }
            $con->commit();
            echo "<script>alert('Order status updated to $status!'); window.location.href='inventory_dashboard.php?tab=orders';</script>";
        } else {
            throw new Exception($con->error);
        }
    } catch (Exception $e) {
        $con->rollback();
        echo "<script>alert('Error updating order: " . $e->getMessage() . "'); window.location.href='inventory_dashboard.php?tab=orders';</script>";
    }
} else {
    header("Location: inventory_dashboard.php?tab=orders");
}
?>
