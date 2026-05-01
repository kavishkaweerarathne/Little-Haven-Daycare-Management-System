<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$child = $con->query("SELECT * FROM children WHERE id = $id AND staff_id = " . $_SESSION['user_id'])->fetch_assoc();

if (!$child) {
    header("Location: staff_dashboard.php?tab=my_class");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $age = (int)$_POST['age'];
    $gender = mysqli_real_escape_string($con, $_POST['gender']);

    $stmt = $con->prepare("UPDATE children SET name=?, age=?, gender=? WHERE id=? AND staff_id=?");
    $stmt->bind_param("sisii", $name, $age, $gender, $id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo "<script>alert('Student profile updated!'); window.location.href='staff_dashboard.php?tab=my_class';</script>";
    } else {
        echo "<script>alert('Error updating student: " . $con->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student | Staff Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0EA5E9; --secondary: #1E293B; --bg: #F8FAFC; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background: white; width: 100%; max-width: 500px; padding: 40px; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
        label { font-weight: 600; font-size: 0.9rem; color: var(--secondary); }
        input, select { padding: 14px; border: 1.5px solid #E2E8F0; border-radius: 12px; font-family: inherit; font-size: 1rem; }
        .btn { background: var(--primary); color: white; padding: 16px; border: none; border-radius: 14px; font-weight: 700; cursor: pointer; width: 100%; transition: 0.3s; margin-top: 10px; }
        .btn:hover { background: #0369A1; transform: translateY(-2px); }
        .back-link { display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; margin-bottom: 25px; font-weight: 500; }
    </style>
</head>
<body>
    <div class="container">
        <a href="staff_dashboard.php?tab=my_class" class="back-link"><i class="fas fa-arrow-left"></i> Back to Class</a>
        <h2 style="margin-bottom: 30px; font-weight: 700;">Edit Student Profile</h2>
        <form method="POST">
            <div class="form-group">
                <label>Student Full Name</label>
                <input type="text" name="name" value="<?php echo $child['name']; ?>" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" value="<?php echo $child['age']; ?>" required min="1" max="12">
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
            <button type="submit" class="btn">Update Profile</button>
        </form>
    </div>
</body>
</html>
