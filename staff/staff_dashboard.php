<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login/login.php");
    exit();
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';
$fullname = $_SESSION['fullname'];
$staff_id = $_SESSION['user_id'];

// Fetch real stats
$total_children = $con->query("SELECT COUNT(*) as count FROM children WHERE staff_id = $staff_id")->fetch_assoc()['count'];
// For now, attendance is just a placeholder until we have an attendance table, but let's assume it's 0
$today_attendance = 0; 
$pending_tasks = 3;

// Fetch data for specific tabs
if ($tab == 'my_class') {
    $children_result = $con->query("SELECT * FROM children WHERE staff_id = $staff_id ORDER BY name ASC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal | Little Haven</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #0EA5E9;
            --primary-dark: #0369A1;
            --secondary: #1E293B;
            --accent: #F59E0B;
            --bg-alt: #F8FAFC;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --radius-md: 24px;
            --radius-sm: 16px;
            --shadow-soft: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            --danger: #EF4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-alt);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 300px;
            background: var(--secondary);
            color: white;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            gap: 40px;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: white;
        }

        .logo-icon {
            font-size: 2rem;
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .nav-item {
            padding: 15px 20px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
        }

        .nav-item i { font-size: 1.2rem; }

        .nav-item:hover, .nav-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-item.active {
            background: var(--primary);
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 300px;
            padding: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            background: white;
            padding: 10px 25px;
            border-radius: 50px;
            box-shadow: var(--shadow-soft);
        }

        .avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .logout-btn {
            color: var(--danger);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            margin-left: 10px;
            border-left: 1px solid #E2E8F0;
            padding-left: 15px;
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 50px;
            border-radius: var(--radius-md);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(14, 165, 233, 0.2);
        }

        .welcome-card h2 { font-size: 2.2rem; margin-bottom: 10px; }
        .welcome-card p { opacity: 0.9; font-size: 1.1rem; max-width: 500px; }

        .welcome-card i.bg-icon {
            position: absolute;
            right: -30px;
            bottom: -30px;
            font-size: 15rem;
            opacity: 0.1;
            transform: rotate(-15deg);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .stat-icon {
            width: 65px;
            height: 65px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .stat-info h3 { font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .stat-info p { font-size: 1.8rem; font-weight: 700; color: var(--secondary); }

        /* Content Sections */
        .card {
            background: white;
            padding: 35px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-soft);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .table-container { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px 20px; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; border-bottom: 2px solid #F1F5F9; }
        td { padding: 20px; border-bottom: 1px solid #F1F5F9; font-size: 0.95rem; }

        .badge { padding: 6px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .badge-success { background: #DCFCE7; color: #166534; }
        .badge-warning { background: #FEF3C7; color: #92400E; }

        .btn-action {
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-danger { background: #FEE2E2; color: #EF4444; }
        .btn-danger:hover { background: #EF4444; color: white; }
        .btn-success { background: #DCFCE7; color: #166534; }
        .btn-success:hover { background: #166534; color: white; }

        /* Settings Container */
        .settings-container {
            display: flex;
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-soft);
            min-height: 550px;
            overflow: hidden;
            border: 1px solid #F1F5F9;
        }

        .settings-sidebar {
            width: 260px;
            background: #F8FAFC;
            padding: 30px 20px;
            border-right: 1px solid #F1F5F9;
        }

        .settings-nav-item {
            padding: 14px 20px;
            border-radius: 14px;
            cursor: pointer;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--text-muted);
            font-weight: 600;
            transition: all 0.3s;
        }

        .settings-nav-item:hover { background: white; color: var(--primary); }
        .settings-nav-item.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .settings-main { flex: 1; padding: 40px; }
        .settings-content { display: none; animation: fadeIn 0.4s ease; }
        .settings-content.active { display: block; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Profile & Security Specifics */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 35px;
            padding-bottom: 25px;
            border-bottom: 1px solid #F1F5F9;
        }

        .avatar-lg {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.2rem;
            font-weight: 700;
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-group label {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .input-group { position: relative; }
        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
        }

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 1.5px solid #E2E8F0;
            border-radius: 14px;
            font-family: inherit;
            font-size: 1rem;
            outline: none;
            background: #F8FAFC;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }

        .input-group input[readonly] { background: #F1F5F9; color: #94A3B8; cursor: not-allowed; }

        .error-text {
            color: var(--danger);
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 5px;
            display: none;
        }
        .error-text.show { display: block; }

        .btn-save {
            background: var(--primary);
            color: white;
            border: none;
            padding: 16px 35px;
            border-radius: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2);
        }

        .btn-save:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(14, 165, 233, 0.3); }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
        }
        .alert-success { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }
        .alert-error { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }

        @media (max-width: 1024px) {
            .sidebar { width: 80px; padding: 30px 15px; }
            .sidebar span, .logo span { display: none; }
            .main-content { margin-left: 80px; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="#" class="logo">
            <i class="fas fa-hands-holding-child logo-icon"></i>
            <span>Little Haven</span>
        </a>
        <div class="nav-links">
            <a href="staff_dashboard.php?tab=overview" class="nav-item <?php echo $tab == 'overview' ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie"></i>
                <span>Overview</span>
            </a>
            <a href="staff_dashboard.php?tab=search" class="nav-item <?php echo $tab == 'search' ? 'active' : ''; ?>">
                <i class="fas fa-search"></i>
                <span>Search Student</span>
            </a>
            <a href="staff_dashboard.php?tab=schedule" class="nav-item <?php echo $tab == 'schedule' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>Schedule</span>
            </a>
            <a href="staff_dashboard.php?tab=activities" class="nav-item <?php echo $tab == 'activities' ? 'active' : ''; ?>">
                <i class="fas fa-list-check"></i>
                <span>Activity Logs</span>
            </a>
            <a href="staff_dashboard.php?tab=settings" class="nav-item <?php echo $tab == 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-user-gear"></i>
                <span>Settings</span>
            </a>
        </div>
    </div>

    <main class="main-content">
        <div class="header">
            <h1 style="font-size: 1.8rem; font-weight: 700;"><?php echo ucfirst(str_replace('_', ' ', $tab)); ?></h1>
            <div class="user-profile">
                <div class="avatar"><?php echo strtoupper(substr($fullname, 0, 1)); ?></div>
                <div style="text-align: left;">
                    <p style="font-weight: 600; font-size: 0.95rem; line-height: 1;"><?php echo $fullname; ?></p>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">Teacher / Staff</span>
                </div>
                <a href="../login/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <?php if ($tab == 'overview'): ?>
            <div class="welcome-card">
                <h2>Hello, <?php echo explode(' ', $fullname)[0]; ?>!</h2>
                <p>Ready for another day of shaping little minds? Here's what's happening today in your class.</p>
                <i class="fas fa-graduation-cap bg-icon"></i>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #E0F2FE; color: var(--primary);"><i class="fas fa-children"></i></div>
                    <div class="stat-info">
                        <h3>My Students</h3>
                        <p><?php echo $total_children; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #DCFCE7; color: #166534;"><i class="fas fa-user-check"></i></div>
                    <div class="stat-info">
                        <h3>Present Today</h3>
                        <p><?php echo $today_attendance; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #FEF3C7; color: #92400E;"><i class="fas fa-tasks"></i></div>
                    <div class="stat-info">
                        <h3>Daily Tasks</h3>
                        <p><?php echo $pending_tasks; ?></p>
                    </div>
                </div>
            </div>

            <?php 
                $today_query = "SELECT * FROM staff_schedule 
                               WHERE staff_id = $staff_id 
                               AND activity_date = CURDATE() 
                               AND status NOT IN ('Completed', 'Cancelled') 
                               ORDER BY start_time ASC";
                $today_res = $con->query($today_query);
            ?>
            <div class="card">
                <div class="section-header">
                    <h3>Today's Schedule</h3>
                    <a href="?tab=schedule" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.9rem;">View All</a>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Activity</th>
                                <th>Room</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($today_res->num_rows > 0): ?>
                                <?php while($row = $today_res->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo date('h:i A', strtotime($row['start_time'])); ?></td>
                                        <td><strong><?php echo $row['activity_name']; ?></strong></td>
                                        <td><?php echo $row['room']; ?></td>
                                        <td>
                                            <?php 
                                                $s_badge = 'badge-warning';
                                                if ($row['status'] == 'Ongoing') $s_badge = 'badge-success';
                                            ?>
                                            <span class="badge <?php echo $s_badge; ?>"><?php echo $row['status']; ?></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" style="text-align: center; padding: 30px; color: var(--text-muted);">No active tasks for today.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>





        <?php elseif ($tab == 'schedule'): 
            $schedule_result = $con->query("SELECT * FROM staff_schedule WHERE staff_id = $staff_id ORDER BY activity_date ASC, start_time ASC");
        ?>
            <div class="card">
                <div class="section-header">
                    <h3>Class Schedule & Events</h3>
                    <div style="display: flex; gap: 15px;">
                        <a href="schedule_report.php" class="btn-action btn-success"><i class="fas fa-file-pdf"></i> Download Report</a>
                        <a href="add_schedule.php" class="btn-action btn-primary"><i class="fas fa-calendar-plus"></i> Add Event</a>
                    </div>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Activity</th>
                                <th>Room/Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($schedule_result->num_rows > 0): ?>
                                <?php while ($row = $schedule_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo date('d M Y', strtotime($row['activity_date'])); ?></strong></td>
                                        <td><?php echo date('h:i A', strtotime($row['start_time'])); ?></td>
                                        <td><strong><?php echo $row['activity_name']; ?></strong></td>
                                        <td><?php echo $row['room']; ?></td>
                                        <td>
                                            <?php 
                                                $s_badge = 'badge-warning';
                                                if ($row['status'] == 'Completed') $s_badge = 'badge-success';
                                                if ($row['status'] == 'Cancelled') $s_badge = 'badge-danger';
                                            ?>
                                            <span class="badge <?php echo $s_badge; ?>"><?php echo $row['status']; ?></span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 8px;">
                                                <a href="edit_schedule.php?id=<?php echo $row['id']; ?>" class="btn-action btn-primary" style="padding: 8px 12px;" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="delete_schedule.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this event?')" class="btn-action btn-danger" style="padding: 8px 12px;" title="Delete"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align: center; padding: 40px;">No events scheduled. Plan your activities now!</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>


        <?php elseif ($tab == 'search'): 
            $search_query = isset($_GET['q']) ? mysqli_real_escape_string($con, $_GET['q']) : '';
            
            // If query is empty, fetch all. Otherwise filter.
            if (empty($search_query)) {
                $q = "SELECT c.*, u.fullname as parent_name, u.email as parent_email, u.phone as parent_phone 
                      FROM children c 
                      LEFT JOIN users u ON c.parent_id = u.id 
                      ORDER BY c.name ASC";
            } else {
                $q = "SELECT c.*, u.fullname as parent_name, u.email as parent_email, u.phone as parent_phone 
                      FROM children c 
                      LEFT JOIN users u ON c.parent_id = u.id 
                      WHERE c.id = '$search_query' OR c.name LIKE '%$search_query%'
                      ORDER BY c.name ASC";
            }
            $search_result = $con->query($q);
        ?>
            <div class="card" style="max-width: 900px; margin: 0 auto;">
                <div class="section-header" style="text-align: center; flex-direction: column; gap: 15px;">
                    <h2 style="font-size: 1.8rem; color: var(--secondary);"><i class="fas fa-user-zoom"></i> Find a Student</h2>
                    <p style="color: var(--text-muted);">Enter Student ID or Full Name to retrieve records</p>
                </div>
                
                <form action="" method="GET" style="margin-top: 20px;">
                    <input type="hidden" name="tab" value="search">
                    <div style="display: flex; gap: 10px; background: var(--bg-alt); padding: 8px; border-radius: 16px; border: 1.5px solid #E2E8F0;">
                        <input type="text" name="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search by Student ID or Name..." style="flex: 1; border: none; background: transparent; padding: 12px 20px; font-size: 1.1rem; outline: none;">
                        <button type="submit" style="background: var(--primary); color: white; border: none; padding: 12px 30px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;"><i class="fas fa-search"></i> Search</button>
                    </div>
                </form>

                <?php if ($search_result): ?>
                    <div style="margin-top: 40px;">
                        <?php if ($search_result->num_rows > 0): ?>
                            <?php while($child = $search_result->fetch_assoc()): ?>
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 20px; padding: 30px; margin-bottom: 25px; display: grid; grid-template-columns: 100px 1fr; gap: 30px;">
                                    <div style="width: 100px; height: 100px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: white; font-weight: 700;">
                                        <?php echo strtoupper(substr($child['name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                                            <div>
                                                <h3 style="font-size: 1.5rem; color: var(--secondary); margin-bottom: 5px;"><?php echo $child['name']; ?></h3>
                                                <span class="badge badge-success">Child ID: #C-<?php echo $child['id']; ?></span>
                                            </div>
                                            <div style="text-align: right;">
                                                <a href="manage_activities.php?child_id=<?php echo $child['id']; ?>" class="btn-action btn-primary"><i class="fas fa-notes-medical"></i> Daily Log</a>
                                            </div>
                                        </div>

                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                                            <div>
                                                <h4 style="font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 12px;">Student Details</h4>
                                                <p style="margin-bottom: 8px;"><strong>Age:</strong> <?php echo $child['age']; ?> Years</p>
                                                <p style="margin-bottom: 8px;"><strong>Gender:</strong> <?php echo ucfirst($child['gender']); ?></p>
                                                <p style="margin-bottom: 8px;"><strong>Enrolled:</strong> <?php echo date('d M Y', strtotime($child['enrolled_date'])); ?></p>
                                            </div>
                                            <div>
                                                <h4 style="font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 12px;">Parent Contact</h4>
                                                <?php if($child['parent_name']): ?>
                                                    <p style="margin-bottom: 8px;"><strong>Name:</strong> <?php echo $child['parent_name']; ?></p>
                                                    <p style="margin-bottom: 8px;"><strong>Phone:</strong> <?php echo $child['parent_phone']; ?></p>
                                                    <p style="margin-bottom: 8px;"><strong>Email:</strong> <?php echo $child['parent_email']; ?></p>
                                                <?php else: ?>
                                                    <p style="color: var(--danger); font-style: italic;">No parent linked to this account.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 50px; background: #fff5f5; border-radius: 20px; border: 1px solid #fed7d7;">
                                <i class="fas fa-user-slash" style="font-size: 3rem; color: #f56565; margin-bottom: 15px;"></i>
                                <h3 style="color: #c53030;">No Student Found</h3>
                                <p style="color: #9b2c2c;">We couldn't find any student matching "<?php echo htmlspecialchars($search_query); ?>". Please check the ID or spelling.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($tab == 'activities'): 
            $search_log = isset($_GET['log_q']) ? mysqli_real_escape_string($con, $_GET['log_q']) : '';
            $date_filter = isset($_GET['log_date']) ? mysqli_real_escape_string($con, $_GET['log_date']) : '';
            
            $where_clauses = [];
            if (!empty($search_log)) $where_clauses[] = "(c.name LIKE '%$search_log%' OR c.id = '$search_log')";
            if (!empty($date_filter)) $where_clauses[] = "da.activity_date = '$date_filter'";
            
            $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
            
            $logs_q = "SELECT da.*, c.name as child_name 
                      FROM daily_activities da 
                      JOIN children c ON da.child_id = c.id 
                      $where_sql
                      ORDER BY da.activity_date DESC, da.id DESC";
            $logs_res = $con->query($logs_q);
        ?>
            <div class="card">
                <div class="section-header" style="flex-direction: column; align-items: stretch; gap: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="font-size: 1.5rem;"><i class="fas fa-history"></i> Student Activity History</h2>
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <span class="badge badge-success"><?php echo $logs_res->num_rows; ?> Total Logs</span>
                            <a href="staff_dashboard.php?tab=search" class="btn-action btn-primary"><i class="fas fa-plus"></i> Add Daily Activity</a>
                        </div>
                    </div>
                    
                    <form action="" method="GET" style="display: grid; grid-template-columns: 1fr 200px 150px; gap: 15px; background: #f8fafc; padding: 20px; border-radius: 20px; border: 1px solid #e2e8f0;">
                        <input type="hidden" name="tab" value="activities">
                        <div style="position: relative;">
                            <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                            <input type="text" name="log_q" value="<?php echo htmlspecialchars($search_log); ?>" placeholder="Search by student name or ID..." style="width: 100%; padding: 12px 12px 12px 45px; border-radius: 12px; border: 1.5px solid #e2e8f0; outline: none;">
                        </div>
                        <input type="date" name="log_date" value="<?php echo htmlspecialchars($date_filter); ?>" style="padding: 12px; border-radius: 12px; border: 1.5px solid #e2e8f0; outline: none;">
                        <button type="submit" style="background: var(--secondary); color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;">Filter Logs</button>
                    </form>
                </div>

                <div class="table-container" style="margin-top: 20px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Mood</th>
                                <th>Meal Details</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($logs_res->num_rows > 0): ?>
                                <?php while($log = $logs_res->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo date('d M Y', strtotime($log['activity_date'])); ?></strong></td>
                                        <td><span style="font-weight: 600; color: var(--secondary);"><?php echo $log['child_name']; ?></span></td>
                                        <td>
                                            <?php 
                                                $m_bg = '#fef3c7'; $m_cl = '#92400e'; $m_ic = '😐';
                                                if($log['mood'] == 'Happy') { $m_bg = '#dcfce7'; $m_cl = '#166534'; $m_ic = '😊'; }
                                                elseif($log['mood'] == 'Excited') { $m_bg = '#e0f2fe'; $m_cl = '#0369a1'; $m_ic = '🤩'; }
                                                elseif($log['mood'] == 'Fussy') { $m_bg = '#fee2e2'; $m_cl = '#ef4444'; $m_ic = '😢'; }
                                            ?>
                                            <span class="badge" style="background: <?php echo $m_bg; ?>; color: <?php echo $m_cl; ?>;">
                                                <?php echo $m_ic . ' ' . $log['mood']; ?>
                                            </span>
                                        </td>
                                        <td><div style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.9rem; color: var(--text-muted);"><?php echo $log['meal_details']; ?></div></td>
                                        <td>
                                            <div style="display: flex; gap: 8px;">
                                                <a href="manage_activities.php?child_id=<?php echo $log['child_id']; ?>&date=<?php echo $log['activity_date']; ?>" class="btn-action btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="delete_activity.php?id=<?php echo $log['id']; ?>" onclick="return confirm('Are you sure you want to delete this activity log?')" class="btn-action btn-danger" title="Delete"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align: center; padding: 60px; color: #94a3b8;">No activity logs match your criteria.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($tab == 'settings'): ?>
            <div class="settings-container">
                <div class="settings-sidebar">
                    <div class="settings-nav-item active" data-settings-tab="profile">
                        <i class="fas fa-user-circle"></i>
                        <span>Profile</span>
                    </div>
                    <div class="settings-nav-item" data-settings-tab="security">
                        <i class="fas fa-shield-halved"></i>
                        <span>Security</span>
                    </div>
                </div>
                <div class="settings-main">
                    <!-- Profile Section -->
                    <div id="settings-profile" class="settings-content active">
                        <div class="profile-header">
                            <div class="avatar-lg">
                                <?php echo strtoupper(substr($fullname, 0, 1)); ?>
                            </div>
                            <div>
                                <h2 style="font-size: 1.5rem; color: var(--secondary); margin-bottom: 5px;">My Profile</h2>
                                <p style="color: var(--text-muted); font-size: 0.95rem;">Update your personal information and contact details.</p>
                            </div>
                        </div>

                        <?php if(isset($_GET['success']) && !isset($_GET['set_tab'])): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($_GET['error']) && !isset($_GET['set_tab'])): ?>
                            <div class="alert alert-error">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                            </div>
                        <?php endif; ?>

                        <form action="update_profile.php" method="POST" id="profileForm">
                            <div style="display: flex; flex-direction: column; gap: 25px;">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label><i class="fas fa-user-tag"></i> Full Name</label>
                                        <div class="input-group">
                                            <i class="fas fa-user"></i>
                                            <input type="text" id="staff_fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
                                        </div>
                                        <span class="error-text" id="fullname-error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-envelope"></i> Email Address</label>
                                        <div class="input-group">
                                            <i class="fas fa-at"></i>
                                            <input type="email" id="staff_email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
                                        </div>
                                        <span class="error-text" id="email-error"></span>
                                    </div>
                                </div>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label><i class="fas fa-phone-volume"></i> Phone Number</label>
                                        <div class="input-group">
                                            <i class="fas fa-phone"></i>
                                            <input type="text" id="staff_phone" name="phone" value="<?php echo htmlspecialchars($_SESSION['phone'] ?? ''); ?>" maxlength="10" required>
                                        </div>
                                        <span class="error-text" id="phone-error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-user-shield"></i> Account Role</label>
                                        <div class="input-group">
                                            <i class="fas fa-shield"></i>
                                            <input type="text" value="Staff / Teacher" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-top: 15px; border-top: 1px solid #F1F5F9; padding-top: 25px;">
                                    <button type="submit" class="btn-save">
                                        <i class="fas fa-save"></i> Save Profile Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Security Section -->
                    <div id="settings-security" class="settings-content">
                        <div class="profile-header">
                            <div class="avatar-lg" style="background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div>
                                <h2 style="font-size: 1.5rem; color: var(--secondary); margin-bottom: 5px;">Security Settings</h2>
                                <p style="color: var(--text-muted); font-size: 0.95rem;">Update your password to keep your account safe.</p>
                            </div>
                        </div>

                        <?php if(isset($_GET['set_tab']) && $_GET['set_tab'] == 'security'): ?>
                            <?php if(isset($_GET['success'])): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                                </div>
                            <?php endif; ?>
                            <?php if(isset($_GET['error'])): ?>
                                <div class="alert alert-error">
                                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <form action="update_password.php" method="POST" id="passwordForm">
                            <div style="display: flex; flex-direction: column; gap: 25px;">
                                <div class="form-group">
                                    <label><i class="fas fa-key"></i> Current Password</label>
                                    <div class="input-group">
                                        <i class="fas fa-lock-open"></i>
                                        <input type="password" id="old_password" name="old_password" placeholder="Enter current password" required>
                                    </div>
                                    <span class="error-text" id="old-password-error"></span>
                                </div>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label><i class="fas fa-lock"></i> New Password</label>
                                        <div class="input-group">
                                            <i class="fas fa-shield-check"></i>
                                            <input type="password" id="new_password" name="new_password" placeholder="Min 4 characters" required>
                                        </div>
                                        <span class="error-text" id="new-password-error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-check-double"></i> Confirm Password</label>
                                        <div class="input-group">
                                            <i class="fas fa-shield-halved"></i>
                                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat new password" required>
                                        </div>
                                        <span class="error-text" id="confirm-password-error"></span>
                                    </div>
                                </div>
                                <div style="margin-top: 15px; border-top: 1px solid #F1F5F9; padding-top: 25px;">
                                    <button type="submit" class="btn-save" style="background: #4F46E5;">
                                        <i class="fas fa-shield-alt"></i> Update Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card" style="text-align: center; padding: 100px;">
                <i class="fas fa-tools" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 20px;"></i>
                <h2><?php echo ucfirst(str_replace('_', ' ', $tab)); ?> Module</h2>
                <p style="color: var(--text-muted);">This module is currently under maintenance. Please check back later.</p>
            </div>
        <?php endif; ?>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Settings Tab Switching
            const settingsTabs = document.querySelectorAll('.settings-nav-item');
            const settingsContents = document.querySelectorAll('.settings-content');

            settingsTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const target = tab.getAttribute('data-settings-tab');
                    
                    settingsTabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    settingsContents.forEach(content => content.classList.remove('active'));
                    document.getElementById('settings-' + target).classList.add('active');
                });
            });

            // Auto-switch to security tab if requested
            <?php if(isset($_GET['set_tab']) && $_GET['set_tab'] == 'security'): ?>
                const securityTabBtn = document.querySelector('[data-settings-tab="security"]');
                if(securityTabBtn) securityTabBtn.click();
            <?php endif; ?>

            // --- Form Validations ---
            const showError = (input, errorElement, message) => {
                if (message) {
                    input.style.borderColor = 'var(--danger)';
                    errorElement.textContent = message;
                    errorElement.classList.add('show');
                } else {
                    input.style.borderColor = '#E2E8F0';
                    errorElement.classList.remove('show');
                    errorElement.textContent = "";
                }
            };

            // Profile Validation
            const profileForm = document.getElementById('profileForm');
            if (profileForm) {
                const fullnameInput = document.getElementById('staff_fullname');
                const phoneInput = document.getElementById('staff_phone');
                const fullnameError = document.getElementById('fullname-error');
                const phoneError = document.getElementById('phone-error');

                fullnameInput.addEventListener('input', () => {
                    fullnameInput.value = fullnameInput.value.replace(/[^a-zA-Z\s]/g, '');
                    if (fullnameInput.value.trim().length < 3) {
                        showError(fullnameInput, fullnameError, "Name must be at least 3 characters.");
                    } else {
                        showError(fullnameInput, fullnameError, "");
                    }
                });

                phoneInput.addEventListener('input', () => {
                    let val = phoneInput.value.replace(/\D/g, '');
                    if (val.length > 10) val = val.substring(0, 10);
                    phoneInput.value = val;
                    if (val.length !== 10) {
                        showError(phoneInput, phoneError, "Phone number must be 10 digits.");
                    } else {
                        showError(phoneInput, phoneError, "");
                    }
                });

                profileForm.addEventListener('submit', (e) => {
                    if (fullnameInput.value.trim().length < 3 || phoneInput.value.length !== 10) {
                        e.preventDefault();
                        alert("Please fix the errors in the profile form.");
                    }
                });
            }

            // Password Validation
            const passwordForm = document.getElementById('passwordForm');
            if (passwordForm) {
                const newPassInput = document.getElementById('new_password');
                const confPassInput = document.getElementById('confirm_password');
                const newPassErr = document.getElementById('new-password-error');
                const confPassErr = document.getElementById('confirm-password-error');

                newPassInput.addEventListener('input', () => {
                    if (newPassInput.value.length < 4) {
                        showError(newPassInput, newPassErr, "Password must be at least 4 characters.");
                    } else {
                        showError(newPassInput, newPassErr, "");
                    }
                });

                confPassInput.addEventListener('input', () => {
                    if (confPassInput.value !== newPassInput.value) {
                        showError(confPassInput, confPassErr, "Passwords do not match.");
                    } else {
                        showError(confPassInput, confPassErr, "");
                    }
                });

                passwordForm.addEventListener('submit', (e) => {
                    if (newPassInput.value.length < 4 || newPassInput.value !== confPassInput.value) {
                        e.preventDefault();
                        alert("Please fix the errors in the security form.");
                    }
                });
            }
        });

        function confirmDeleteChild(id) {
            if (confirm('Are you sure you want to remove this student from your class? This action cannot be undone.')) {
                window.location.href = 'delete_child.php?id=' + id;
            }
        }
    </script>
</body>
</html>
