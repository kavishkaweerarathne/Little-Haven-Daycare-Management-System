<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0ea5e9;
            --bg: #f0f9ff;
            --text: #0c4a6e;
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
            border-right: 1px solid #bae6fd;
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
            <p><i class="fas fa-chalkboard-user"></i> Dashboard</p>
            <p><i class="fas fa-users"></i> Manage Classes</p>
            <p><i class="fas fa-clipboard-check"></i> Attendance</p>
            <p><i class="fas fa-message"></i> Messages</p>
        </nav>
    </div>
    <div class="main-content">
        <div class="header">
            <h1>Staff Dashboard</h1>
            <a href="../login/logout.php" class="logout-btn">Logout</a>
        </div>
        <div class="welcome-card">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h2>
            <p>This is the staff portal for managing your classes and daycare operations.</p>
        </div>
    </div>
</body>
</html>
