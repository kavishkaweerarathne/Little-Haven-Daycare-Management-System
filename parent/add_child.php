<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login/login.php");
    exit();
}

// Fetch parent details from session or database
$parent_id = $_SESSION['user_id'];
$parent_name = $_SESSION['fullname'];
$parent_email = $_SESSION['email'] ?? 'Not set';
$parent_phone = $_SESSION['phone'] ?? 'Not set';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $child_name = mysqli_real_escape_string($con, $_POST['child_name']);
    $age = (int)$_POST['age'];
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $enrolled_date = date('Y-m-d');

    // We'll store the child's basic info and link it to the parent
    // The user mentioned parent name, email, phone in the form - we'll handle them as context
    $stmt = $con->prepare("INSERT INTO children (name, age, gender, parent_id, enrolled_date, staff_id) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("sisss", $child_name, $age, $gender, $parent_id, $enrolled_date);

    if ($stmt->execute()) {
        echo "<script>alert('Child registered successfully!'); window.location.href='parent_dashboard.php?tab=children';</script>";
    } else {
        echo "<script>alert('Error: " . $con->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Registration | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #26C6DA; --secondary: #1A5276; --bg: #F7FAFC; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg); color: #1e293b; margin: 0; padding: 20px; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .registration-card { background: white; width: 100%; max-width: 600px; padding: 3rem; border-radius: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; }
        .header { text-align: center; margin-bottom: 2.5rem; }
        .header h2 { font-size: 2rem; color: var(--secondary); margin-bottom: 10px; font-weight: 700; }
        .header p { color: #64748b; font-size: 1rem; }
        .section-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; color: var(--primary); letter-spacing: 1px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; }
        .section-title::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 1.5rem; }
        .form-group.full { grid-column: span 2; }
        label { font-weight: 600; font-size: 0.9rem; color: #475569; }
        input, select { padding: 14px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-family: inherit; font-size: 1rem; outline: none; transition: 0.3s; background: #f8fafc; }
        input:focus, select:focus { border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(38, 198, 218, 0.1); }
        input[readonly] { background: #f1f5f9; cursor: not-allowed; color: #94a3b8; }
        .btn-submit { background: var(--primary); color: white; border: none; padding: 16px; border-radius: 16px; font-weight: 700; font-size: 1.1rem; cursor: pointer; width: 100%; transition: 0.3s; box-shadow: 0 10px 20px rgba(38, 198, 218, 0.2); }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(38, 198, 218, 0.3); }
        .back-btn { display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: #64748b; font-weight: 600; margin-bottom: 2rem; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="registration-card">
        <a href="parent_dashboard.php?tab=children" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <div class="header">
            <h2>Child Registration</h2>
            <p>Please provide the following details to register your child.</p>
        </div>
        
        <form method="POST">
            <div class="section-title">Parent Information</div>
            <div class="form-group">
                <label>Parent Name</label>
                <input type="text" value="<?php echo $parent_name; ?>" readonly>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" value="<?php echo $parent_email; ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" value="<?php echo $parent_phone; ?>" readonly>
                </div>
            </div>

            <div class="section-title">Child Information</div>
            <div class="form-group">
                <label>Child's Full Name</label>
                <input type="text" name="child_name" placeholder="Enter child's name" required>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" placeholder="Age" min="1" max="15" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female" selected>Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-submit">Complete Registration</button>
        </form>
    </div>
</body>
</html>
