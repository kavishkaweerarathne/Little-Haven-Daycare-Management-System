<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_SESSION['user_id'];
    $activity_name = mysqli_real_escape_string($con, $_POST['activity_name']);
    $activity_date = mysqli_real_escape_string($con, $_POST['activity_date']);
    $start_time = mysqli_real_escape_string($con, $_POST['start_time']);
    $room = mysqli_real_escape_string($con, $_POST['room']);
    $status = mysqli_real_escape_string($con, $_POST['status']);

    $sql = "INSERT INTO staff_schedule (staff_id, activity_name, activity_date, start_time, room, status) 
            VALUES ('$staff_id', '$activity_name', '$activity_date', '$start_time', '$room', '$status')";

    if (mysqli_query($con, $sql)) {
        echo "<script>alert('Event scheduled successfully!'); window.location.href='staff_dashboard.php?tab=schedule';</script>";
    } else {
        echo "<script>alert('Error scheduling event: " . mysqli_error($con) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Schedule Event | Little Haven</title>
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
                <h2>Add New Event</h2>
                <p style="color: #64748b; margin: 5px 0 0;">Plan an activity for your class schedule.</p>
            </div>
            <div style="width: 55px; height: 55px; background: #E0F2FE; color: var(--primary); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fas fa-calendar-plus"></i>
            </div>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Activity / Event Name</label>
                <input type="text" name="activity_name" placeholder="e.g. Story Time, Outdoor Play" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="activity_date" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="start_time" required value="09:00">
                </div>
            </div>

            <div class="form-group">
                <label>Room / Location</label>
                <input type="text" name="room" placeholder="e.g. Sunflower Room, Playground" required>
            </div>

            <div class="form-group">
                <label>Initial Status</label>
                <select name="status">
                    <option value="Upcoming">Upcoming</option>
                    <option value="Ongoing">Ongoing</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Save Event to Schedule</button>
        </form>
    </div>
</body>
</html>
