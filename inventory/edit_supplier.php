<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$supplier = $con->query("SELECT * FROM suppliers WHERE id = $id")->fetch_assoc();

if (!$supplier) {
    header("Location: inventory_dashboard.php?tab=suppliers");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $contact_person = mysqli_real_escape_string($con, $_POST['contact_person']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $category = mysqli_real_escape_string($con, $_POST['category']);

    $stmt = $con->prepare("UPDATE suppliers SET name=?, contact_person=?, phone=?, email=?, address=?, category=? WHERE id=?");
    $stmt->bind_param("ssssssi", $name, $contact_person, $phone, $email, $address, $category, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Supplier updated successfully!'); window.location.href='inventory_dashboard.php?tab=suppliers';</script>";
    } else {
        echo "<script>alert('Error updating supplier: " . $con->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier | Inventory</title>
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
        input, select, textarea { padding: 12px; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; }
        .btn { background: var(--secondary); color: white; padding: 15px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; margin-top: 20px; width: 100%; transition: 0.3s; }
        .btn:hover { background: var(--primary); transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="margin-bottom: 30px;"><a href="inventory_dashboard.php?tab=suppliers" style="color: inherit; text-decoration: none;"><i class="fas fa-arrow-left"></i></a> Edit Supplier</h2>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Supplier Name</label>
                    <input type="text" name="name" value="<?php echo $supplier['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Contact Person</label>
                    <input type="text" name="contact_person" value="<?php echo $supplier['contact_person']; ?>">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="Supplies" <?php echo $supplier['category'] == 'Supplies' ? 'selected' : ''; ?>>Supplies</option>
                        <option value="Food" <?php echo $supplier['category'] == 'Food' ? 'selected' : ''; ?>>Food</option>
                        <option value="Cleaning" <?php echo $supplier['category'] == 'Cleaning' ? 'selected' : ''; ?>>Cleaning</option>
                        <option value="Toys" <?php echo $supplier['category'] == 'Toys' ? 'selected' : ''; ?>>Toys</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?php echo $supplier['phone']; ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $supplier['email']; ?>">
                </div>
                <div class="form-group full-width">
                    <label>Address</label>
                    <textarea name="address" rows="3"><?php echo $supplier['address']; ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn">Update Supplier</button>
        </form>
    </div>
</body>
</html>
