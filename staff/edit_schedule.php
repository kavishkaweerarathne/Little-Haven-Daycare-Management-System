<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$staff_id = $_SESSION['user_id'];

// Fetch the event
$sql = "SELECT * FROM staff_schedule WHERE id = $id";
$result = mysqli_query($con, $sql);
$event = mysqli_fetch_assoc($result);

if (!$event) {
    header("Location: staff_dashboard.php?tab=schedule");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $activity_name = mysqli_real_escape_string($con, $_POST['activity_name']);
    $activity_date = mysqli_real_escape_string($con, $_POST['activity_date']);
    $start_time = mysqli_real_escape_string($con, $_POST['start_time']);
    $room = mysqli_real_escape_string($con, $_POST['room']);
    $status = mysqli_real_escape_string($con, $_POST['status']);

    $update_sql = "UPDATE staff_schedule SET 
                   activity_name = '$activity_name', 
                   activity_date = '$activity_date', 
                   start_time = '$start_time', 
                   room = '$room', 
                   status = '$status' 
                   WHERE id = $id";

    if (mysqli_query($con, $update_sql)) {
        echo "<script>alert('Event updated successfully!'); window.location.href='staff_dashboard.php?tab=schedule';</script>";
    } else {
        echo "<script>alert('Error updating event: " . mysqli_error($con) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedule Event | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0EA5E9; --secondary: #1E293B; --bg: #F8FAFC; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; margin: 0; }
        .container { background: white; width: 100%; max-width: 600px; padding: 40px; border-radius: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        .header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h2 { font-weight: 700; color: var(--secondary); margin: 0; }
        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
        label { font-weight: 600; font-size: 0.9rem; color: #475569; }
        input, select { padding: 14px; border: 1.5px solid #E2E8F0; border-radius: 12px; font-family: inherit; font-size: 1rem; outline: none; transition: 0.3s; background: #f8fafc; }
        input:focus, select:focus { border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1); }
        .btn-submit { background: var(--primary); color: white; border: none; padding: 16px; border-radius: 16px; font-weight: 700; cursor: pointer; width: 100%; transition: 0.3s; font-size: 1.1rem; box-shadow: 0 10px 15px rgba(14, 165, 233, 0.2); margin-top: 10px; }
        .btn-submit:hover { background: #0369A1; transform: translateY(-2px); }
        .back-link { display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; margin-bottom: 25px; font-weight: 600; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <a href="staff_dashboard.php?tab=schedule" class="back-link"><i class="fas fa-arrow-left"></i> Back to Schedule</a>
        <div class="header">
            <div>
                <h2>Edit Event</h2>
                <p style="color: #64748b; margin: 5px 0 0;">Modify the details of your scheduled activity.</p>
            </div>
            <div style="width: 55px; height: 55px; background: #E0F2FE; color: var(--primary); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fas fa-edit"></i>
            </div>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Activity / Event Name</label>
                <input type="text" name="activity_name" value="<?php echo htmlspecialchars($event['activity_name']); ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="activity_date" value="<?php echo $event['activity_date']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="start_time" value="<?php echo date('H:i', strtotime($event['start_time'])); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Room / Location</label>
                <input type="text" name="room" value="<?php echo htmlspecialchars($event['room']); ?>" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="Upcoming" <?php echo $event['status'] == 'Upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="Ongoing" <?php echo $event['status'] == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="Completed" <?php echo $event['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="Cancelled" <?php echo $event['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Update Event Details</button>
        </form>
    </div>
</body>
</html>
