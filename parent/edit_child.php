<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$parent_id = $_SESSION['user_id'];

// Ensure the child belongs to this parent
$query = "SELECT * FROM children WHERE id = $id AND parent_id = $parent_id";
$result = mysqli_query($con, $query);
$child = mysqli_fetch_assoc($result);

if (!$child) {
    header("Location: parent_dashboard.php?tab=children");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $age = (int)$_POST['age'];
    $gender = mysqli_real_escape_string($con, $_POST['gender']);

    $stmt = $con->prepare("UPDATE children SET name = ?, age = ?, gender = ? WHERE id = ? AND parent_id = ?");
    $stmt->bind_param("sisii", $name, $age, $gender, $id, $parent_id);

    if ($stmt->execute()) {
        echo "<script>alert('Child information updated!'); window.location.href='parent_dashboard.php?tab=children';</script>";
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
    <title>Edit Child | Parent Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #26C6DA; --secondary: #1A5276; --bg: #F7FAFC; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; }
        .card { background: white; width: 100%; max-width: 500px; padding: 40px; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
        .header { margin-bottom: 30px; text-align: center; }
        .header h2 { color: var(--secondary); font-weight: 700; margin-bottom: 8px; }
        .form-group { margin-bottom: 20px; display: flex; flex-direction: column; gap: 8px; }
        label { font-weight: 600; font-size: 0.9rem; color: #64748b; }
        input, select { padding: 14px; border: 1.5px solid #E2E8F0; border-radius: 12px; font-family: inherit; font-size: 1rem; outline: none; transition: 0.3s; }
        input:focus, select:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(38, 198, 218, 0.1); }
        .btn { background: var(--primary); color: white; padding: 16px; border: none; border-radius: 14px; font-weight: 700; cursor: pointer; width: 100%; transition: 0.3s; font-size: 1rem; }
        .btn:hover { background: #00ACC1; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(38, 198, 218, 0.2); }
        .back-link { display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; margin-bottom: 25px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <a href="parent_dashboard.php?tab=children" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <div class="header">
            <h2>Edit Child Details</h2>
            <p style="color: #64748b;">Update information for <?php echo $child['name']; ?>.</p>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>Child's Full Name</label>
                <input type="text" name="name" value="<?php echo $child['name']; ?>" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" value="<?php echo $child['age']; ?>" required min="1" max="15">
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="male" <?php echo $child['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo $child['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo $child['gender'] == 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn">Update Information</button>
        </form>
    </div>
</body>
</html>
