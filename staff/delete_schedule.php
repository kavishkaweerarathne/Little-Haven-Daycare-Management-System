<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $staff_id = $_SESSION['user_id'];
    
    // Delete the event only if it belongs to this staff member
    $sql = "DELETE FROM staff_schedule WHERE id = $id AND staff_id = $staff_id";
    
    if (mysqli_query($con, $sql)) {
        header("Location: staff_dashboard.php?tab=schedule&success=Event deleted successfully!");
    } else {
        header("Location: staff_dashboard.php?tab=schedule&error=Error deleting event: " . mysqli_error($con));
    }
} else {
    header("Location: staff_dashboard.php?tab=schedule");
}
exit();
?>
