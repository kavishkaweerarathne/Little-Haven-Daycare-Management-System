<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $message = mysqli_real_escape_string($con, $_POST['message']);

    if (empty($first_name) || empty($last_name) || empty($email) || empty($message)) {
        header("Location: contact.php?status=error&message=required");
        exit;
    }

    $sql = "INSERT INTO inquiries (first_name, last_name, email, subject, message) 
            VALUES ('$first_name', '$last_name', '$email', '$subject', '$message')";

    if ($con->query($sql)) {
        header("Location: contact.php?status=success");
    } else {
        header("Location: contact.php?status=error&message=db");
    }
} else {
    header("Location: contact.php");
}
exit;
?>

