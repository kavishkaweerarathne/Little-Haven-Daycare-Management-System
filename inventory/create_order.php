<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

$suppliers = $con->query("SELECT id, name FROM suppliers");
$prefill_item = $_GET['item'] ?? '';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = mysqli_real_escape_string($con, $_POST['item_name']);
    $supplier_id = $_POST['supplier_id'] ?: 'NULL';
    $quantity = (int)$_POST['quantity'];
    $order_date = $_POST['order_date'];
    $total_cost = $_POST['total_cost'] ?: 0;

    // Server-side validation
    if (empty($item_name)) {
        $error = 'Item name is required.';
    } elseif ($quantity <= 0) {
        $error = 'Quantity must be greater than zero.';
    } elseif ($total_cost < 0) {
        $error = 'Total cost cannot be negative.';
    } else {
        $query = "INSERT INTO inventory_orders (item_name, supplier_id, quantity, order_date, total_cost, status) VALUES ('$item_name', $supplier_id, $quantity, '$order_date', $total_cost, 'Pending')";
        
        if ($con->query($query)) {
            $success = 'Order placed successfully!';
        } else {
            $error = 'Error placing order: ' . $con->error;
        }
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #FF9F1C; --secondary: #264653; --bg: #F7FAFC; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background: white; width: 100%; max-width: 600px; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        label { font-weight: 600; font-size: 0.9rem; color: var(--secondary); }
        input, select { padding: 12px; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; transition: 0.3s; }
        input:focus, select:focus { border-color: var(--primary); outline: none; }
        .btn { background: var(--secondary); color: white; padding: 15px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; margin-top: 20px; width: 100%; transition: 0.3s; }
        .btn:hover { background: var(--primary); transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="margin-bottom: 30px;"><a href="inventory_dashboard.php?tab=orders" style="color: inherit; text-decoration: none;"><i class="fas fa-arrow-left"></i></a> Create Supply Order</h2>
        <form method="POST" id="orderForm">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Item Name</label>
                    <input type="text" name="item_name" id="item_name" value="<?php echo $prefill_item; ?>" required placeholder="e.g. Baby Diapers (L)">
                </div>
                <div class="form-group">
                    <label>Supplier</label>
                    <select name="supplier_id" id="supplier_id" required>
                        <option value="">Select Supplier</option>
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
                    <input type="number" id="unit_price" step="0.01" required min="0.01" oninput="calculateTotal()" placeholder="0.00">
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
        const qty = parseFloat(document.getElementById('quantity').value) || 0;
        const price = parseFloat(document.getElementById('unit_price').value) || 0;
        const total = qty * price;
        document.getElementById('total_cost').value = total.toFixed(2);
    }

    document.getElementById('orderForm').addEventListener('submit', function(e) {
        const qty = parseFloat(document.getElementById('quantity').value);
        const price = parseFloat(document.getElementById('unit_price').value);
        const supplier = document.getElementById('supplier_id').value;

        if (!supplier) {
            e.preventDefault();
            Swal.fire('Error', 'Please select a supplier!', 'error');
            return;
        }

        if (isNaN(qty) || qty <= 0) {
            e.preventDefault();
            Swal.fire('Error', 'Quantity must be greater than zero!', 'error');
            return;
        }

        if (isNaN(price) || price < 0) {
            e.preventDefault();
            Swal.fire('Error', 'Unit price cannot be negative!', 'error');
            return;
        }
    });

    <?php if($success): ?>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo $success; ?>',
            icon: 'success'
        }).then(() => {
            window.location.href = 'inventory_dashboard.php?tab=orders';
        });
    <?php endif; ?>

    <?php if($error): ?>
        Swal.fire('Error', '<?php echo $error; ?>', 'error');
    <?php endif; ?>
    </script>
</body>
</html>
