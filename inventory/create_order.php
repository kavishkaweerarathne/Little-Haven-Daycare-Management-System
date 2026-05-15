<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

$suppliers = $con->query("SELECT id, name FROM suppliers");
$prefill_item = $_GET['item'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = mysqli_real_escape_string($con, $_POST['item_name']);
    $supplier_id = $_POST['supplier_id'] ?: 'NULL';
    $quantity = (int)$_POST['quantity'];
    $order_date = $_POST['order_date'];
    $total_cost = $_POST['total_cost'] ?: 0;

    $query = "INSERT INTO inventory_orders (item_name, supplier_id, quantity, order_date, total_cost, status) VALUES ('$item_name', $supplier_id, $quantity, '$order_date', $total_cost, 'Pending')";
    
    if ($con->query($query)) {
        echo "<script>alert('Order placed successfully!'); window.location.href='inventory_dashboard.php?tab=orders';</script>";
    } else {
        echo "<script>alert('Error placing order: " . $con->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order | Inventory</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #FF9F1C; --secondary: #264653; --bg: #F7FAFC; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background: white; width: 100%; max-width: 600px; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        label { font-weight: 600; font-size: 0.9rem; color: var(--secondary); }
        input, select { padding: 12px; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; }
        .btn { background: var(--secondary); color: white; padding: 15px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; margin-top: 20px; width: 100%; transition: 0.3s; }
        .btn:hover { background: var(--primary); transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="margin-bottom: 30px;"><a href="inventory_dashboard.php?tab=orders" style="color: inherit; text-decoration: none;"><i class="fas fa-arrow-left"></i></a> Create Supply Order</h2>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Item Name</label>
                    <input type="text" name="item_name" value="<?php echo $prefill_item; ?>" required placeholder="e.g. Baby Diapers (L)">
                </div>
                <div class="form-group">
                    <label>Supplier</label>
                    <select name="supplier_id">
                        <option value="">Select Supplier (Optional)</option>
                        <?php while ($s = $suppliers->fetch_assoc()): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="quantity" id="quantity" required min="1" oninput="calculateTotal()">
                </div>
                <div class="form-group">
                    <label>Unit Price (Rs.)</label>
                    <input type="number" id="unit_price" step="0.01" oninput="calculateTotal()" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Order Date</label>
                    <input type="date" name="order_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Total Cost (Rs.)</label>
                    <input type="number" name="total_cost" id="total_cost" step="0.01" readonly style="background: #f8fafc; font-weight: 700;">
                </div>
            </div>
            <button type="submit" class="btn">Place Order</button>
        </form>
    </div>

    <script>
        function calculateTotal() {
            const qty = document.getElementById('quantity').value || 0;
            const price = document.getElementById('unit_price').value || 0;
            const total = qty * price;
            document.getElementById('total_cost').value = total.toFixed(2);
        }
    </script>
</body>
</html>
