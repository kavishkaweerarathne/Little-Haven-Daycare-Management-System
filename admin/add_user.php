<?php
include '../config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}

$default_role = isset($_GET['role']) ? $_GET['role'] : 'parent';
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $password = $_POST['password'];
    $role = mysqli_real_escape_string($con, $_POST['role']);

    // Check if email exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $check_email);
    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Email already registered!'); window.history.back();</script>";
        exit();
    }

    // Since the user changed registration to plain text, I'll follow that for consistency
    // but I'll add a comment about it.
    $sql = "INSERT INTO users (fullname, email, phone, password, role) VALUES ('$fullname', '$email', '$phone', '$password', '$role')";
    
    if (mysqli_query($con, $sql)) {
        echo "<script>alert('User added successfully!'); window.location.href='admin_dashboard.php?tab=$tab';</script>";
    } else {
        echo "<script>alert('Error adding user: " . mysqli_error($con) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #26C6DA;
            --bg: #F7FAFC;
            --text: #1A202C;
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
        .add-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        h2 { margin-top: 0; color: var(--primary); }
        .form-group { margin-bottom: 1.2rem; }
        label { display: block; margin-bottom: 0.4rem; font-weight: 600; font-size: 0.9rem; }
        input, select {
            width: 100%;
            padding: 10px;
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
            margin-top: 1rem;
        }
        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #6b7280;
            text-decoration: none;
            font-size: 0.9rem;
        }
        /* Error Styles */
        .error-text {
            color: #ff4d4d;
            font-size: 0.82rem;
            font-weight: 500;
            margin-top: 5px;
            display: block;
            height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
            opacity: 0;
        }
        .error-text.show {
            height: auto;
            margin-top: 5px;
            opacity: 1;
        }
        input.error, select.error {
            border-color: #ff4d4d;
            background-color: #fffafa;
        }
    </style>
</head>
<body>
    <div class="add-card">
        <h2>Add New <?php echo ucfirst($default_role); ?></h2>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter full name" required>
                <span class="error-text" id="fullname-error"></span>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="email" name="email" placeholder="example@gmail.com" required>
                <span class="error-text" id="email-error"></span>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" id="phone" name="phone" placeholder="07XXXXXXXX" maxlength="10" required>
                <span class="error-text" id="phone-error"></span>
            </div>
            <div class="form-group">
                <label>Temporary Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <span class="error-text" id="password-error"></span>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="parent" <?php if($default_role == 'parent') echo 'selected'; ?>>Parent</option>
                    <option value="staff" <?php if($default_role == 'staff') echo 'selected'; ?>>Staff</option>
                    <option value="finance" <?php if($default_role == 'finance') echo 'selected'; ?>>Finance Manager</option>
                    <option value="inventory" <?php if($default_role == 'inventory') echo 'selected'; ?>>Inventory Manager</option>
                </select>
            </div>
            <button type="submit" class="btn-save">Create User</button>
            <a href="admin_dashboard.php?tab=<?php echo $tab; ?>" class="btn-cancel">Cancel</a>
        </form>
    </div>
    <script src="add_user.js"></script>
</body>
</html>
