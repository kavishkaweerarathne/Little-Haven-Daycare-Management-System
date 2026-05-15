<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $child_ids = $_POST['child_ids'];
    $check_in_times = $_POST['check_in_times'];
    $check_out_times = $_POST['check_out_times'];
    $attendance_date = date('Y-m-d');

    $con->begin_transaction();

    try {
        foreach ($child_ids as $index => $child_id) {
            $check_in = $check_in_times[$index];
            $check_out = $check_out_times[$index];

            // If both are empty, we might want to delete an existing record if it was cleared, 
            // but usually we just skip or keep existing. Let's assume we update if at least one is present.
            if (empty($check_in) && empty($check_out)) {
                // Optional: delete existing if both are cleared
                $con->query("DELETE FROM attendance WHERE child_id = $child_id AND attendance_date = '$attendance_date'");
                continue;
            }

            // Check if attendance already exists for today
            $check = $con->query("SELECT id FROM attendance WHERE child_id = $child_id AND attendance_date = '$attendance_date'");
            
            if ($check->num_rows > 0) {
                $row = $check->fetch_assoc();
                $stmt = $con->prepare("UPDATE attendance SET check_in_time = ?, check_out_time = ? WHERE id = ?");
                // Convert empty strings to NULL for database
                $val_in = !empty($check_in) ? $check_in : null;
                $val_out = !empty($check_out) ? $check_out : null;
                $stmt->bind_param("ssi", $val_in, $val_out, $row['id']);
            } else {
                $stmt = $con->prepare("INSERT INTO attendance (child_id, attendance_date, check_in_time, check_out_time) VALUES (?, ?, ?, ?)");
                $val_in = !empty($check_in) ? $check_in : null;
                $val_out = !empty($check_out) ? $check_out : null;
                $stmt->bind_param("isss", $child_id, $attendance_date, $val_in, $val_out);
            }
            $stmt->execute();
        }
        $con->commit();
        echo "<script>alert('Attendance logs updated successfully!'); window.location.href='staff_dashboard.php?tab=attendance';</script>";
    } catch (Exception $e) {
        $con->rollback();
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
?>
