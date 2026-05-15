<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = mysqli_real_escape_string($con, $_POST['item_name']);
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $unit = mysqli_real_escape_string($con, $_POST['unit']);
    $supplier_name = mysqli_real_escape_string($con, $_POST['supplier_name']);

    $stmt = $con->prepare("INSERT INTO inventory (item_name, category, quantity, unit, supplier_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $item_name, $category, $quantity, $unit, $supplier_name);

    if ($stmt->execute()) {
        echo "<script>alert('Item added successfully!'); window.location.href='inventory_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding item: " . $con->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Item | Inventory</title>
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
            <h2>Add New Inventory Item</h2>
        </div>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Item Name</label>
                    <input type="text" name="item_name" required placeholder="e.g. Baby Diapers">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="Food & Beverages">Food & Beverages</option>
                        <option value="Toys & Games">Toys & Games</option>
                        <option value="Educational Materials">Educational Materials</option>
                        <option value="Stationery Supplies">Stationery Supplies</option>
                        <option value="Cleaning Supplies">Cleaning Supplies</option>
                        <option value="Hygiene & Sanitation Products">Hygiene & Sanitation Products</option>
                        <option value="Furniture & Equipment">Furniture & Equipment</option>
                        <option value="Medical & First Aid Supplies">Medical & First Aid Supplies</option>
                        <option value="Baby Care Products">Baby Care Products</option>
                        <option value="Kitchen Supplies">Kitchen Supplies</option>
                        <option value="Sleeping & Rest Items">Sleeping & Rest Items</option>
                        <option value="Outdoor Play Equipment">Outdoor Play Equipment</option>
                        <option value="Arts & Crafts Materials">Arts & Crafts Materials</option>
                        <option value="Uniforms & Staff Essentials">Uniforms & Staff Essentials</option>
                        <option value="Safety & Security Equipment">Safety & Security Equipment</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Unit</label>
                    <input type="text" name="unit" value="pcs" required>
                </div>
                <div class="form-group">
                    <label>Current Quantity</label>
                    <input type="number" name="quantity" required min="0">
                </div>
                <div class="form-group full-width">
                    <label>Supplier Name</label>
                    <input type="text" name="supplier_name">
                </div>
            </div>
            <button type="submit" class="submit-btn">Save Item</button>
        </form>
    </div>
</body>
</html>
