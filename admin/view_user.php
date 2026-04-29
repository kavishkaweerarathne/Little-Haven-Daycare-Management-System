<?php
include '../config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php?tab=$tab");
    exit();
}

$id = mysqli_real_escape_string($con, $_GET['id']);
$sql = "SELECT * FROM users WHERE id = '$id'";
$result = mysqli_query($con, $sql);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "<script>alert('User not found!'); window.location.href='admin_dashboard.php?tab=$tab';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile | <?php echo htmlspecialchars($user['fullname']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --bg: #f8fafc;
            --text: #1e293b;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 2rem;
            display: flex;
            justify-content: center;
        }
        .profile-container {
            width: 100%;
            max-width: 600px;
            background: white;
            padding: 3rem;
            border-radius: 2rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            position: relative;
        }
        .back-btn {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .avatar {
            width: 120px;
            height: 120px;
            background: #e0e7ff;
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 1.5rem;
        }
        .role-badge {
            background: #4f46e5;
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .detail-group {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-label {
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        .detail-value {
            font-size: 1.1rem;
            font-weight: 600;
        }
        .action-footer {
            display: flex;
            gap: 1rem;
            margin-top: 3rem;
        }
        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
        }
        .btn-edit { background: var(--primary); color: white; }
        .btn-delete { background: #fef2f2; color: #ef4444; }
        .btn-edit:hover { opacity: 0.9; }
        .btn-delete:hover { background: #fee2e2; }
    </style>
</head>
<body>
    <div class="profile-container">
        <a href="admin_dashboard.php?tab=<?php echo $tab; ?>" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
        
        <div class="profile-header">
            <div class="avatar">
                <i class="fas fa-user"></i>
            </div>
            <h1><?php echo htmlspecialchars($user['fullname']); ?></h1>
            <span class="role-badge"><?php echo htmlspecialchars($user['role']); ?></span>
        </div>

        <div class="detail-group">
            <div class="detail-label">Full Name</div>
            <div class="detail-value"><?php echo htmlspecialchars($user['fullname']); ?></div>
        </div>

        <div class="detail-group">
            <div class="detail-label">Email Address</div>
            <div class="detail-value"><?php echo htmlspecialchars($user['email']); ?></div>
        </div>

        <div class="detail-group">
            <div class="detail-label">Phone Number</div>
            <div class="detail-value"><?php echo htmlspecialchars($user['phone']); ?></div>
        </div>

        <div class="detail-group">
            <div class="detail-label">User ID</div>
            <div class="detail-value">#<?php echo htmlspecialchars($user['id']); ?></div>
        </div>

        <div class="action-footer">
            <a href="edit_user.php?id=<?php echo $user['id']; ?>&tab=<?php echo $tab; ?>" class="btn btn-edit">Edit Profile</a>
            <a href="#" onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo $tab; ?>')" class="btn btn-delete">Delete User</a>
        </div>
    </div>

    <script>
    function confirmDelete(id, tab) {
        if (confirm("Are you sure you want to delete this user? This action cannot be undone.")) {
            window.location.href = 'delete_user.php?id=' + id + '&tab=' + tab;
        }
    }
    </script>
</body>
</html>
