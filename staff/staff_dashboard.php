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
            <a href="staff_dashboard.php?tab=my_class" class="nav-item <?php echo $tab == 'my_class' ? 'active' : ''; ?>">
                <i class="fas fa-school"></i>
                <span>My Class</span>
            </a>
            <a href="staff_dashboard.php?tab=search" class="nav-item <?php echo $tab == 'search' ? 'active' : ''; ?>">
                <i class="fas fa-search"></i>
                <span>Search Student</span>
            </a>
            <a href="staff_dashboard.php?tab=attendance" class="nav-item <?php echo $tab == 'attendance' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-user"></i>
                <span>Attendance</span>
            </a>
            <a href="staff_dashboard.php?tab=schedule" class="nav-item <?php echo $tab == 'schedule' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>Schedule</span>
            </a>
            <a href="staff_dashboard.php?tab=daily_log" class="nav-item <?php echo $tab == 'daily_log' ? 'active' : ''; ?>">
                <i class="fas fa-book"></i>
                <span>Daily Logs</span>
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
                <h2>Hello, Teacher <?php echo explode(' ', $fullname)[0]; ?>!</h2>
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
                            <tr>
                                <td>09:00 AM</td>
                                <td><strong>Morning Circle</strong></td>
                                <td>Sunflower Room</td>
                                <td><span class="badge badge-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>10:30 AM</td>
                                <td><strong>Art & Craft</strong></td>
                                <td>Sunflower Room</td>
                                <td><span class="badge badge-warning">Ongoing</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($tab == 'my_class'): ?>
            <div class="card">
                <div class="section-header">
                    <h3>My Students (<?php echo $total_children; ?> Total)</h3>
                    <div style="display: flex; gap: 15px;">
                        <div class="search-box" style="background: var(--bg-alt); padding: 10px 20px; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-search" style="color: var(--text-muted);"></i>
                            <input type="text" id="studentSearch" placeholder="Search students..." style="border: none; background: transparent; outline: none; width: 150px;">
                        </div>
                        <a href="add_child.php" class="btn-action btn-primary"><i class="fas fa-plus"></i> Add Student</a>
                    </div>
                </div>
                <div class="table-container">
                    <table id="studentTable">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Enrolled Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($children_result->num_rows > 0): ?>
                                <?php while ($child = $children_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo $child['name']; ?></strong></td>
                                        <td><?php echo $child['age']; ?> Years</td>
                                        <td><?php echo ucfirst($child['gender']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($child['enrolled_date'])); ?></td>
                                        <td>
                                            <div style="display: flex; gap: 8px;">
                                                <a href="edit_child.php?id=<?php echo $child['id']; ?>" class="btn-action btn-primary" title="Edit Profile"><i class="fas fa-user-edit"></i></a>
                                                <a href="manage_activities.php?child_id=<?php echo $child['id']; ?>" class="btn-action btn-success" title="Daily Activities"><i class="fas fa-notes-medical"></i></a>
                                                <a href="javascript:void(0)" onclick="confirmDeleteChild(<?php echo $child['id']; ?>)" class="btn-action btn-danger" title="Delete"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align: center; padding: 40px;">No students found in your class.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($tab == 'attendance'): 
            $attendance_date = date('Y-m-d');
            $children_result = $con->query("SELECT * FROM children WHERE staff_id = $staff_id ORDER BY name ASC");
        ?>
            <div class="card">
                <form action="save_attendance.php" method="POST">
                    <div class="section-header">
                        <div>
                            <h3>Mark Attendance</h3>
                            <p style="color: var(--text-muted); font-size: 0.9rem;"><?php echo date('l, d F Y'); ?></p>
                        </div>
                        <button type="submit" class="btn-action btn-primary" style="padding: 12px 25px;"><i class="fas fa-save"></i> Save Attendance</button>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Status</th>
                                    <th>Check-in Time</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($children_result->num_rows > 0): ?>
                                    <?php while ($child = $children_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong><?php echo $child['name']; ?></strong></td>
                                            <td>
                                                <input type="hidden" name="child_ids[]" value="<?php echo $child['id']; ?>">
                                                <select name="status[]" style="padding: 8px; border-radius: 8px; border: 1.5px solid #E2E8F0; outline: none;">
                                                    <option value="Present">Present</option>
                                                    <option value="Absent">Absent</option>
                                                    <option value="Late">Late</option>
                                                </select>
                                            </td>
                                            <td><input type="time" name="check_in_time[]" value="<?php echo date('H:i'); ?>" style="padding: 8px; border-radius: 8px; border: 1.5px solid #E2E8F0;"></td>
                                            <td><input type="text" name="notes[]" placeholder="Add note..." style="padding: 8px; border-radius: 8px; border: 1.5px solid #E2E8F0; width: 100%;"></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" style="text-align: center; padding: 40px;">No students found to mark attendance.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
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
                                            <a href="delete_schedule.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this event?')" class="btn-action btn-danger" style="padding: 8px 12px;"><i class="fas fa-trash"></i></a>
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
        <?php elseif ($tab == 'daily_log'): 
            $logs_result = $con->query("
                SELECT da.*, c.name as child_name 
                FROM daily_activities da 
                JOIN children c ON da.child_id = c.id 
                WHERE c.staff_id = $staff_id 
                ORDER BY da.activity_date DESC, c.name ASC
            ");
        ?>
            <div class="card">
                <div class="section-header">
                    <h3>Activity History</h3>
                    <div style="display: flex; gap: 15px;">
                        <div class="search-box" style="background: var(--bg-alt); padding: 10px 20px; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-calendar-day" style="color: var(--text-muted);"></i>
                            <input type="date" id="dateFilter" style="border: none; background: transparent; outline: none;">
                        </div>
                    </div>
                </div>
                <div class="table-container">
                    <table id="activityTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Mood</th>
                                <th>Activities</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($logs_result->num_rows > 0): ?>
                                <?php while ($log = $logs_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo date('d M Y', strtotime($log['activity_date'])); ?></td>
                                        <td><strong><?php echo $log['child_name']; ?></strong></td>
                                        <td>
                                            <?php 
                                                $mood_class = 'badge-warning';
                                                $icon = '😐';
                                                if ($log['mood'] == 'Happy') { $mood_class = 'badge-success'; $icon = '😊'; }
                                                elseif ($log['mood'] == 'Excited') { $mood_class = 'badge-success'; $icon = '🤩'; }
                                                elseif ($log['mood'] == 'Fussy') { $mood_class = 'badge-danger'; $icon = '😢'; }
                                            ?>
                                            <span class="badge <?php echo $mood_class; ?>"><?php echo $icon . ' ' . $log['mood']; ?></span>
                                        </td>
                                        <td><div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo $log['activities']; ?></div></td>
                                        <td>
                                            <a href="manage_activities.php?child_id=<?php echo $log['child_id']; ?>" class="btn-action btn-primary" title="Edit/View"><i class="fas fa-eye"></i> View</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align: center; padding: 40px;">No activity logs found. Start by marking activities in 'My Class'.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($tab == 'search'): 
            $search_query = isset($_GET['q']) ? mysqli_real_escape_string($con, $_GET['q']) : '';
            $search_result = null;
            if (!empty($search_query)) {
                $q = "SELECT c.*, u.fullname as parent_name, u.email as parent_email, u.phone as parent_phone 
                      FROM children c 
                      LEFT JOIN users u ON c.parent_id = u.id 
                      WHERE c.id = '$search_query' OR c.name LIKE '%$search_query%'";
                $search_result = $con->query($q);
            }
        ?>
            <div class="card" style="max-width: 900px; margin: 0 auto;">
                <div class="section-header" style="text-align: center; flex-direction: column; gap: 15px;">
                    <h2 style="font-size: 1.8rem; color: var(--secondary);"><i class="fas fa-user-zoom"></i> Find a Student</h2>
                    <p style="color: var(--text-muted);">Enter Student ID or Full Name to retrieve records</p>
                </div>
                
                <form action="" method="GET" style="margin-top: 20px;">
                    <input type="hidden" name="tab" value="search">
                    <div style="display: flex; gap: 10px; background: var(--bg-alt); padding: 8px; border-radius: 16px; border: 1.5px solid #E2E8F0;">
                        <input type="text" name="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search by ID or Name..." style="flex: 1; border: none; background: transparent; padding: 12px 20px; font-size: 1.1rem; outline: none;" required>
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
        <?php elseif ($tab == 'settings'): ?>
            <div class="card" style="max-width: 600px;">
                <h3 style="margin-bottom: 25px;">Profile Settings</h3>
                <form action="update_settings.php" method="POST">
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 600; font-size: 0.9rem;">Full Name</label>
                            <input type="text" name="fullname" value="<?php echo $fullname; ?>" required style="padding: 14px; border-radius: 12px; border: 1.5px solid #E2E8F0; width: 100%;">
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 600; font-size: 0.9rem;">Current Password</label>
                            <input type="password" name="current_password" placeholder="Enter current password" style="padding: 14px; border-radius: 12px; border: 1.5px solid #E2E8F0; width: 100%;">
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 600; font-size: 0.9rem;">New Password</label>
                            <input type="password" name="new_password" placeholder="Enter new password" style="padding: 14px; border-radius: 12px; border: 1.5px solid #E2E8F0; width: 100%;">
                        </div>
                        <button type="submit" style="background: var(--secondary); color: white; padding: 16px; border-radius: 14px; border: none; font-weight: 700; cursor: pointer; margin-top: 10px;">Update Profile</button>
                    </div>
                </form>
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
        function confirmDeleteChild(id) {
            if (confirm('Are you sure you want to remove this student from your class? This action cannot be undone.')) {
                window.location.href = 'delete_child.php?id=' + id;
            }
        }

        const studentSearch = document.getElementById('studentSearch');
        if (studentSearch) {
            studentSearch.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll('#studentTable tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        }

        const dateFilter = document.getElementById('dateFilter');
        if (dateFilter) {
            dateFilter.addEventListener('change', function() {
                const selectedDate = this.value; // YYYY-MM-DD
                const rows = document.querySelectorAll('#activityTable tbody tr');
                rows.forEach(row => {
                    const dateText = row.cells[0].textContent.trim();
                    // Need to match date formats or use data attributes
                    // Simple approach: show all if empty, else match (assuming display is 'd M Y')
                    // For better matching, let's just check if the row content includes the year/month/day
                    if (!selectedDate) {
                        row.style.display = '';
                        return;
                    }
                    // Extracting parts from YYYY-MM-DD
                    const [y, m, d] = selectedDate.split('-');
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    const formattedDate = `${d} ${months[parseInt(m)-1]} ${y}`;
                    
                    row.style.display = dateText === formattedDate ? '' : 'none';
                });
            });
        }
    </script>
</body>
</html>
