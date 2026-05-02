<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $child_ids = $_POST['child_ids'];
    $statuses = $_POST['status'];
    $check_in_times = $_POST['check_in_time'];
    $notes = $_POST['notes'];
    $attendance_date = date('Y-m-d');

    $con->begin_transaction();

    try {
        foreach ($child_ids as $index => $child_id) {
            $status = $statuses[$index];
            $check_in = $check_in_times[$index];
            $note = mysqli_real_escape_string($con, $notes[$index]);

            // Check if attendance already exists for today
            $check = $con->query("SELECT id FROM attendance WHERE child_id = $child_id AND attendance_date = '$attendance_date'");
            
            if ($check->num_rows > 0) {
                $row = $check->fetch_assoc();
                $stmt = $con->prepare("UPDATE attendance SET status = ?, check_in_time = ?, notes = ? WHERE id = ?");
                $stmt->bind_param("sssi", $status, $check_in, $note, $row['id']);
            } else {
                $stmt = $con->prepare("INSERT INTO attendance (child_id, attendance_date, status, check_in_time, notes) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $child_id, $attendance_date, $status, $check_in, $note);
            }
            $stmt->execute();
        }
        $con->commit();
        echo "<script>alert('Attendance saved successfully!'); window.location.href='staff_dashboard.php?tab=attendance';</script>";
    } catch (Exception $e) {
        $con->rollback();
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
?>
