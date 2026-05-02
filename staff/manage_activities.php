<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login/login.php");
    exit();
}

$child_id = $_GET['child_id'] ?? 0;
// Allow staff to manage logs for any child found via search or in their class
$child = $con->query("SELECT * FROM children WHERE id = $child_id")->fetch_assoc();

if (!$child) {
    header("Location: staff_dashboard.php?tab=my_class");
    exit();
}

$selected_date = $_GET['date'] ?? date('Y-m-d');
// Fetch existing log for selected date
$log = $con->query("SELECT * FROM daily_activities WHERE child_id = $child_id AND activity_date = '$selected_date'")->fetch_assoc();

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
        $stmt->bind_param("issssss", $child_id, $selected_date, $meal_details, $nap_details, $mood, $activities, $notes);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Daily log saved successfully!'); window.location.href='staff_dashboard.php?tab=daily_log';</script>";
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
    <title>Manage Daily Logs | <?php echo $child['name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0EA5E9; --secondary: #1E293B; --bg: #F8FAFC; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; margin: 0; }
        .container { background: white; width: 100%; max-width: 650px; padding: 40px; border-radius: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        .header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-start; }
        .header-text h2 { font-weight: 700; color: var(--secondary); margin-bottom: 5px; }
        .header-text p { color: #64748b; margin: 0; }
        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
        label { font-weight: 600; font-size: 0.9rem; color: #475569; }
        input, select, textarea { padding: 14px; border: 1.5px solid #E2E8F0; border-radius: 12px; font-family: inherit; font-size: 1rem; outline: none; transition: 0.3s; background: #f8fafc; }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1); }
        .btn-save { background: var(--primary); color: white; border: none; padding: 16px; border-radius: 16px; font-weight: 700; cursor: pointer; width: 100%; transition: 0.3s; font-size: 1.1rem; box-shadow: 0 10px 15px rgba(14, 165, 233, 0.2); }
        .btn-save:hover { background: #0369A1; transform: translateY(-2px); }
        .back-link { display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; margin-bottom: 25px; font-weight: 600; font-size: 0.9rem; }
        .date-selector { background: #eff6ff; padding: 15px; border-radius: 16px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px; border: 1px solid #bfdbfe; }
    </style>
</head>
<body>
    <div class="container">
        <a href="staff_dashboard.php?tab=daily_log" class="back-link"><i class="fas fa-arrow-left"></i> Back to History</a>
        
        <div class="header">
            <div class="header-text">
                <h2>Daily Activity Log</h2>
                <p>Student: <strong><?php echo $child['name']; ?></strong> (ID: #C-<?php echo $child['id']; ?>)</p>
            </div>
            <div style="width: 60px; height: 60px; background: var(--primary); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                <i class="fas fa-book-open"></i>
            </div>
        </div>

        <div class="date-selector">
            <label style="margin: 0; color: #1e40af;">Select Date:</label>
            <input type="date" id="logDate" value="<?php echo $selected_date; ?>" onchange="changeDate(this.value)" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #bfdbfe; font-size: 0.9rem;">
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Child's Mood</label>
                <select name="mood" required>
                    <option value="Happy" <?php echo @$log['mood'] == 'Happy' ? 'selected' : ''; ?>>😊 Happy & Energetic</option>
                    <option value="Calm" <?php echo @$log['mood'] == 'Calm' ? 'selected' : ''; ?>>😐 Calm & Quiet</option>
                    <option value="Fussy" <?php echo @$log['mood'] == 'Fussy' ? 'selected' : ''; ?>>😢 Fussy / Tired</option>
                    <option value="Excited" <?php echo @$log['mood'] == 'Excited' ? 'selected' : ''; ?>>🤩 Very Excited</option>
                    <option value="Sad" <?php echo @$log['mood'] == 'Sad' ? 'selected' : ''; ?>>😔 Sad / Emotional</option>
                </select>
            </div>

            <div class="form-group">
                <label>Meals & Nutrition (What did they eat?)</label>
                <textarea name="meal_details" rows="3" placeholder="Describe meals, quantity, and appetite..."><?php echo @$log['meal_details']; ?></textarea>
            </div>

            <div class="form-group">
                <label>Nap / Rest Duration</label>
                <input type="text" name="nap_details" placeholder="e.g. 1 hour (12:30 PM - 1:30 PM)" value="<?php echo @$log['nap_details']; ?>">
            </div>

            <div class="form-group">
                <label>Activities & Learning</label>
                <textarea name="activities" rows="3" placeholder="What games or lessons did they participate in?"><?php echo @$log['activities']; ?></textarea>
            </div>

            <div class="form-group">
                <label>Important Notes for Parents</label>
                <textarea name="notes" rows="2" placeholder="Any specific messages for the family..."><?php echo @$log['notes']; ?></textarea>
            </div>

            <button type="submit" class="btn-save"><?php echo $log ? 'Update Daily Log' : 'Save Daily Log'; ?></button>
        </form>
    </div>

    <script>
        function changeDate(date) {
            window.location.href = 'manage_activities.php?child_id=<?php echo $child_id; ?>&date=' + date;
        }
    </script>
</body>
</html>
