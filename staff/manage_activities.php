<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login/login.php");
    exit();
}

$child_id = $_GET['child_id'] ?? 0;
$child = $con->query("SELECT * FROM children WHERE id = $child_id AND staff_id = " . $_SESSION['user_id'])->fetch_assoc();

if (!$child) {
    header("Location: staff_dashboard.php?tab=my_class");
    exit();
}

$today = date('Y-m-d');
// Fetch existing log for today if any
$log = $con->query("SELECT * FROM daily_activities WHERE child_id = $child_id AND activity_date = '$today'")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $meal_details = mysqli_real_escape_string($con, $_POST['meal_details']);
    $nap_details = mysqli_real_escape_string($con, $_POST['nap_details']);
    $mood = mysqli_real_escape_string($con, $_POST['mood']);
    $activities = mysqli_real_escape_string($con, $_POST['activities']);
    $notes = mysqli_real_escape_string($con, $_POST['notes']);

    if ($log) {
        $stmt = $con->prepare("UPDATE daily_activities SET meal_details=?, nap_details=?, mood=?, activities=?, notes=? WHERE id=?");
        $stmt->bind_param("sssssi", $meal_details, $nap_details, $mood, $activities, $notes, $log['id']);
    } else {
        $stmt = $con->prepare("INSERT INTO daily_activities (child_id, activity_date, meal_details, nap_details, mood, activities, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $child_id, $today, $meal_details, $nap_details, $mood, $activities, $notes);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Daily log saved!'); window.location.href='staff_dashboard.php?tab=my_class';</script>";
    } else {
        echo "<script>alert('Error saving log: " . $con->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Activities | <?php echo $child['name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0EA5E9; --secondary: #1E293B; --bg: #F8FAFC; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background: white; width: 100%; max-width: 600px; padding: 40px; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
        label { font-weight: 600; font-size: 0.9rem; color: var(--secondary); }
        input, select, textarea { padding: 14px; border: 1.5px solid #E2E8F0; border-radius: 12px; font-family: inherit; font-size: 1rem; }
        .btn { background: var(--primary); color: white; padding: 16px; border: none; border-radius: 14px; font-weight: 700; cursor: pointer; width: 100%; transition: 0.3s; margin-top: 10px; }
        .btn:hover { background: #0369A1; transform: translateY(-2px); }
        .back-link { display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; margin-bottom: 25px; font-weight: 500; }
        .header { margin-bottom: 30px; }
        .header h2 { font-weight: 700; color: var(--secondary); }
        .header p { color: var(--text-muted); }
    </style>
</head>
<body>
    <div class="container">
        <a href="staff_dashboard.php?tab=my_class" class="back-link"><i class="fas fa-arrow-left"></i> Back to Class</a>
        <div class="header">
            <h2>Daily Log: <?php echo $child['name']; ?></h2>
            <p>Managing activities for <?php echo date('d M Y'); ?></p>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>Mood Today</label>
                <select name="mood">
                    <option value="Happy" <?php echo @$log['mood'] == 'Happy' ? 'selected' : ''; ?>>😊 Happy & Energetic</option>
                    <option value="Calm" <?php echo @$log['mood'] == 'Calm' ? 'selected' : ''; ?>>😐 Calm & Quiet</option>
                    <option value="Fussy" <?php echo @$log['mood'] == 'Fussy' ? 'selected' : ''; ?>>😢 Fussy / Tired</option>
                    <option value="Excited" <?php echo @$log['mood'] == 'Excited' ? 'selected' : ''; ?>>🤩 Very Excited</option>
                </select>
            </div>
            <div class="form-group">
                <label>Meals & Nutrition</label>
                <textarea name="meal_details" rows="2" placeholder="What did they eat? Any issues?"><?php echo @$log['meal_details']; ?></textarea>
            </div>
            <div class="form-group">
                <label>Nap / Rest Details</label>
                <input type="text" name="nap_details" placeholder="e.g. 12:30 PM to 02:00 PM" value="<?php echo @$log['nap_details']; ?>">
            </div>
            <div class="form-group">
                <label>Activities Participated</label>
                <textarea name="activities" rows="2" placeholder="e.g. Art session, Outdoor play"><?php echo @$log['activities']; ?></textarea>
            </div>
            <div class="form-group">
                <label>Additional Notes for Parents</label>
                <textarea name="notes" rows="2"><?php echo @$log['notes']; ?></textarea>
            </div>
            <button type="submit" class="btn">Save Daily Log</button>
        </form>
    </div>
</body>
</html>
