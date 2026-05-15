<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$stmt = $con->prepare("SELECT * FROM inventory WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "<script>alert('Item not found!'); window.location.href='inventory_dashboard.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = mysqli_real_escape_string($con, $_POST['item_name']);
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $unit = mysqli_real_escape_string($con, $_POST['unit']);
    $supplier_name = mysqli_real_escape_string($con, $_POST['supplier_name']);

    $update_stmt = $con->prepare("UPDATE inventory SET item_name = ?, category = ?, quantity = ?, unit = ?, supplier_name = ? WHERE id = ?");
    $update_stmt->bind_param("ssissi", $item_name, $category, $quantity, $unit, $supplier_name, $id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Item updated successfully!'); window.location.href='inventory_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating item: " . $con->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item | Inventory</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF9F1C;
            --primary-dark: #E76F51;
            --secondary: #264653;
            --bg-alt: #F7FAFC;
            --text-main: #1A202C;
            --radius-md: 20px;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-alt);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .form-container {
            background: white;
            width: 100%;
            max-width: 600px;
            padding: 40px;
            border-radius: var(--radius-md);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        }

        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .header a {
            color: var(--text-main);
            text-decoration: none;
            font-size: 1.2rem;
            transition: transform 0.2s;
        }

        .header a:hover { transform: translateX(-5px); }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .full-width { grid-column: span 2; }

        label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--secondary);
        }

        input, select {
            padding: 12px 15px;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            font-family: inherit;
            outline: none;
        }

        input:focus { border-color: var(--primary); }

        .submit-btn {
            background: var(--secondary);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="header">
            <a href="inventory_dashboard.php"><i class="fas fa-arrow-left"></i></a>
            <h2>Edit Inventory Item</h2>
        </div>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Item Name</label>
                    <input type="text" name="item_name" value="<?php echo $item['item_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="Supplies" <?php echo $item['category'] == 'Supplies' ? 'selected' : ''; ?>>Supplies</option>
                        <option value="Food" <?php echo $item['category'] == 'Food' ? 'selected' : ''; ?>>Food & Snacks</option>
                        <option value="Cleaning" <?php echo $item['category'] == 'Cleaning' ? 'selected' : ''; ?>>Cleaning Materials</option>
                        <option value="Toys" <?php echo $item['category'] == 'Toys' ? 'selected' : ''; ?>>Toys & Educational</option>
                        <option value="Medical" <?php echo $item['category'] == 'Medical' ? 'selected' : ''; ?>>Medical Supplies</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Unit</label>
                    <input type="text" name="unit" value="<?php echo $item['unit']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Current Quantity</label>
                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" required min="0">
                </div>
                <div class="form-group full-width">
                    <label>Supplier Name</label>
                    <input type="text" name="supplier_name" value="<?php echo $item['supplier_name']; ?>">
                </div>
            </div>
            <button type="submit" class="submit-btn">Update Item</button>
        </form>
    </div>
</body>
</html>
