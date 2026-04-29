<?php
include '../config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php?tab=$tab");
    exit();
}

$id = mysqli_real_escape_string($con, $_GET['id']);
$sql = "SELECT * FROM users WHERE id = '$id'";
$result = mysqli_query($con, $sql);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "<script>alert('User not found!'); window.location.href='admin_dashboard.php?tab=$tab';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $role = mysqli_real_escape_string($con, $_POST['role']);

    $update_sql = "UPDATE users SET fullname='$fullname', email='$email', phone='$phone', role='$role' WHERE id='$id'";
    
    if (mysqli_query($con, $update_sql)) {
        echo "<script>alert('User updated successfully!'); window.location.href='admin_dashboard.php?tab=$tab';</script>";
    } else {
        echo "<script>alert('Error updating user: " . mysqli_error($con) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --bg: #f3f4f6;
            --text: #1f2937;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .edit-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        h2 { margin-top: 0; color: var(--primary); }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: inherit;
        }
        .btn-save {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-weight: 700;
            font-size: 1rem;
            transition: opacity 0.3s;
        }
        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #6b7280;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="edit-card">
        <h2>Edit User Details</h2>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="parent" <?php if($user['role'] == 'parent') echo 'selected'; ?>>Parent</option>
                    <option value="staff" <?php if($user['role'] == 'staff') echo 'selected'; ?>>Staff</option>
                </select>
            </div>
            <button type="submit" class="btn-save">Save Changes</button>
            <a href="admin_dashboard.php?tab=<?php echo $tab; ?>" class="btn-cancel">Cancel</a>
        </form>
    </div>
</body>
</html>
