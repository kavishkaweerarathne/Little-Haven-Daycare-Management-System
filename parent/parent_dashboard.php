<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --bg: #f8fafc;
            --text: #1e293b;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 260px;
            background: white;
            border-right: 1px solid #e2e8f0;
            padding: 2rem;
        }
        .main-content {
            flex: 1;
            padding: 2rem;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .welcome-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .logout-btn {
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Little Haven</h2>
        <nav>
            <p><i class="fas fa-home"></i> Dashboard</p>
            <p><i class="fas fa-child"></i> My Children</p>
            <p><i class="fas fa-calendar"></i> Attendance</p>
            <p><i class="fas fa-file-invoice"></i> Billing</p>
        </nav>
    </div>
    <div class="main-content">
        <div class="header">
            <h1>Parent Dashboard</h1>
            <a href="../login/logout.php" class="logout-btn">Logout</a>
        </div>
        <div class="welcome-card">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h2>
            <p>This is your parent portal where you can manage your child's activities and attendance.</p>
        </div>
    </div>
</body>
</html>
