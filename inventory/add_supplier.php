<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inventory') {
    header("Location: ../login/login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $category = mysqli_real_escape_string($con, $_POST['category']);

    // Server-side validation
    if (empty($name)) {
        $error = 'Supplier name is required.';
    } elseif (!empty($phone) && !preg_match("/^[0-9]{10}$/", $phone)) {
        $error = 'Phone number must be exactly 10 digits.';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $con->prepare("INSERT INTO suppliers (name, phone, email, address, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $phone, $email, $address, $category);

        if ($stmt->execute()) {
            $success = 'Supplier added successfully!';
        } else {
            $error = 'Error adding supplier: ' . $con->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier | Inventory</title>
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
        input, select, textarea { padding: 12px; border: 1.5px solid #E2E8F0; border-radius: 10px; font-family: inherit; transition: 0.3s; }
        input:focus, select:focus { border-color: var(--primary); outline: none; }
        .btn { background: var(--secondary); color: white; padding: 15px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; margin-top: 20px; width: 100%; transition: 0.3s; }
        .btn:hover { background: var(--primary); transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="margin-bottom: 30px;"><a href="inventory_dashboard.php?tab=suppliers" style="color: inherit; text-decoration: none;"><i class="fas fa-arrow-left"></i></a> Add New Supplier</h2>
        <form method="POST" id="supplierForm">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Supplier Name</label>
                    <input type="text" name="name" id="name" required placeholder="Enter supplier name">
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
                    <label>Phone Number (10 digits)</label>
                    <input type="text" name="phone" id="phone" placeholder="e.g. 0712345678" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="email" placeholder="e.g. supplier@example.com">
                </div>
                <div class="form-group full-width">
                    <label>Office Address</label>
                    <textarea name="address" rows="3" placeholder="Enter supplier address"></textarea>
                </div>
            </div>
            <button type="submit" class="btn">Save Supplier</button>
        </form>
    </div>

    <script>
    document.getElementById('supplierForm').addEventListener('submit', function(e) {
        const phone = document.getElementById('phone').value;
        const email = document.getElementById('email').value;
        const name = document.getElementById('name').value;

        if (!name.trim()) {
            e.preventDefault();
            Swal.fire('Error', 'Supplier name is required!', 'error');
            return;
        }

        if (phone && !/^[0-9]{10}$/.test(phone)) {
            e.preventDefault();
            Swal.fire('Error', 'Phone number must be exactly 10 digits!', 'error');
            return;
        }

        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            e.preventDefault();
            Swal.fire('Error', 'Please enter a valid email address!', 'error');
            return;
        }
    });

    <?php if($success): ?>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo $success; ?>',
            icon: 'success'
        }).then(() => {
            window.location.href = 'inventory_dashboard.php?tab=suppliers';
        });
    <?php endif; ?>

    <?php if($error): ?>
        Swal.fire('Error', '<?php echo $error; ?>', 'error');
    <?php endif; ?>
    </script>
</body>
</html>
