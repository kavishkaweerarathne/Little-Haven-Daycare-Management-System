<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];

// Fetch linked children
$children = [];
$children_query = "SELECT c.*, u.fullname as staff_name FROM children c 
                   LEFT JOIN users u ON c.staff_id = u.id 
                   WHERE c.parent_id = $user_id";
$children_res = mysqli_query($con, $children_query);
if ($children_res) {
    while($row = mysqli_fetch_assoc($children_res)) {
        $children[] = $row;
    }
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent and Child Dashboard | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="parent_dashboard.css">
    <style>
        .profile-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }
        .modal-content {
            background: white;
            padding: 2.5rem;
            border-radius: 24px;
            width: 90%;
            max-width: 600px;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }
        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748b;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-label { font-weight: 600; color: #64748b; }
        .detail-value { font-weight: 500; color: #1e293b; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fas fa-hands-holding-child"></i> <span>Little Haven</span></h2>
        <nav>
            <p class="<?php echo $tab == 'dashboard' ? 'active' : ''; ?>" data-tab="dashboard"><i class="fas fa-chart-line"></i> <span>Dashboard</span></p>
            <p class="<?php echo $tab == 'children' ? 'active' : ''; ?>" data-tab="children"><i class="fas fa-baby"></i> <span>My Children</span></p>
            <p class="<?php echo $tab == 'activities' ? 'active' : ''; ?>" data-tab="activities"><i class="fas fa-book-open"></i> <span>Daily Activities</span></p>
            <p class="<?php echo $tab == 'notifications' ? 'active' : ''; ?>" data-tab="notifications"><i class="fas fa-bell"></i> <span>Notifications</span></p>
            <p class="<?php echo $tab == 'billing' ? 'active' : ''; ?>" data-tab="billing"><i class="fas fa-file-invoice-dollar"></i> <span>Billing</span></p>
            <p class="<?php echo $tab == 'settings' ? 'active' : ''; ?>" data-tab="settings"><i class="fas fa-user-gear"></i> <span>Settings</span></p>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <h1 id="tab-title">Parent & Child Overview</h1>
            <div style="display: flex; align-items: center; gap: 20px;">
                <div class="user-info" style="text-align: right;">
                    <p style="margin:0; font-weight: 700;"><?php echo $fullname; ?></p>
                    <span style="font-size: 0.8rem; color: #64748b;">Parent Account</span>
                </div>
                <a href="../login/logout.php" class="logout-btn"><i class="fas fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboard-tab" class="tab-content <?php echo $tab == 'dashboard' ? 'active' : ''; ?>">
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h2>Welcome Back! 👋</h2>
                    <p>Track your child's daily activities and stay connected with their growth.</p>
                </div>
                <div class="welcome-date" style="text-align: right;">
                    <h3 style="margin:0; font-size: 1.5rem;"><?php echo date('l'); ?></h3>
                    <p style="margin:0; opacity: 0.8;"><?php echo date('jS F, Y'); ?></p>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--primary);"><i class="fas fa-children"></i></div>
                    <div>
                        <h3 style="margin:0; font-size: 0.85rem; color: #64748b; font-weight: 600;">Linked Children</h3>
                        <p style="margin:0; font-size: 1.8rem; font-weight: 700;"><?php echo count($children); ?></p>
                    </div>
                </div>
                <?php 
                $today = date('Y-m-d');
                $attendance_today = 0;
                if(!empty($children)) {
                    $cids = array_column($children, 'id');
                    $cid_list = implode(',', $cids);
                    $att_res = $con->query("SELECT COUNT(*) as count FROM attendance WHERE child_id IN ($cid_list) AND attendance_date = '$today' AND check_in_time IS NOT NULL");
                    $attendance_today = $att_res->fetch_assoc()['count'];
                }
                ?>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #10b981;"><i class="fas fa-calendar-check"></i></div>
                    <div>
                        <h3 style="margin:0; font-size: 0.85rem; color: #64748b; font-weight: 600;">Today's Attendance</h3>
                        <p style="margin:0; font-size: 1.8rem; font-weight: 700;"><?php echo $attendance_today; ?>/<?php echo count($children); ?></p>
                    </div>
                </div>
                <?php 
                $total_pending = 0;
                if(!empty($children)) {
                    $bill_res = $con->query("SELECT SUM(total_monthly_fee) as total FROM billing WHERE child_id IN ($cid_list) AND payment_status = 'pending'");
                    $total_pending = $bill_res->fetch_assoc()['total'] ?? 0;
                }
                ?>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f59e0b;"><i class="fas fa-receipt"></i></div>
                    <div>
                        <h3 style="margin:0; font-size: 0.85rem; color: #64748b; font-weight: 600;">Pending Invoices</h3>
                        <p style="margin:0; font-size: 1.8rem; font-weight: 700;">Rs. <?php echo number_format($total_pending, 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="left-col">
                    <div class="card">
                        <div class="card-header">
                            <h3>Latest Activity Updates</h3>
                            <a href="?tab=dashboard" style="color: var(--primary); text-decoration: none; font-size: 0.85rem; font-weight: 600;">Refresh</a>
                        </div>
                        <div class="activity-list">
                            <?php if(empty($children)): ?>
                                <p style="text-align: center; color: #64748b; padding: 2rem;">No children linked to your account yet.</p>
                            <?php else: ?>
                                <?php foreach($children as $child): 
                                    $c_id = $child['id'];
                                    $act_res = $con->query("SELECT * FROM daily_activities WHERE child_id = $c_id ORDER BY activity_date DESC LIMIT 1");
                                    $act = $act_res->fetch_assoc();
                                ?>
                                    <div class="child-activity-group" style="margin-bottom: 2rem; border-left: 4px solid var(--primary); padding-left: 1.5rem;">
                                        <h4 style="margin-bottom: 1rem; color: var(--secondary); display: flex; align-items: center; gap: 10px;">
                                            <i class="fas fa-child"></i> <?php echo $child['name']; ?>
                                        </h4>
                                        <?php if($act): ?>
                                            <div class="activity-item">
                                                <div class="activity-point" style="background: #10b981;"></div>
                                                <div class="activity-details">
                                                    <p><strong>Mood Today:</strong> <?php echo $act['mood']; ?></p>
                                                    <p><strong>Activities:</strong> <?php echo $act['activities']; ?></p>
                                                    <p><strong>Staff Note:</strong> <?php echo $act['notes']; ?></p>
                                                    <span style="font-size: 0.75rem; color: #94a3b8;"><?php echo date('d M Y', strtotime($act['activity_date'])); ?></span>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <p style="font-size: 0.9rem; color: #94a3b8; font-style: italic;">No activity logged for today yet.</p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="card" style="margin-top: 2rem;">
                        <div class="card-header">
                            <h3>Recent School Events</h3>
                            <a href="javascript:void(0)" onclick="document.querySelector('[data-tab=\'notifications\']').click()" style="color: var(--primary); text-decoration: none; font-size: 0.85rem; font-weight: 600;">View All</a>
                        </div>
                        <div class="notification-preview">
                            <?php 
                            $recent_notif = mysqli_query($con, "SELECT s.*, st.fullname as staff_name FROM staff_schedule s JOIN users st ON s.staff_id = st.id ORDER BY s.activity_date DESC LIMIT 2");
                            if ($recent_notif && mysqli_num_rows($recent_notif) > 0):
                                while($rn = mysqli_fetch_assoc($recent_notif)):
                            ?>
                                <div style="padding: 15px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 15px; align-items: center;">
                                    <div style="width: 40px; height: 40px; background: #e0f2fe; color: #0ea5e9; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div>
                                        <h5 style="margin: 0; font-size: 0.95rem;"><?php echo $rn['activity_name']; ?></h5>
                                        <p style="margin: 3px 0 0; font-size: 0.8rem; color: #64748b;"><?php echo date('d M', strtotime($rn['activity_date'])); ?> • <?php echo $rn['staff_name']; ?></p>
                                    </div>
                                </div>
                            <?php endwhile; else: ?>
                                <p style="text-align: center; color: #94a3b8; padding: 1rem; font-size: 0.9rem;">No recent events.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                </div>
                <div class="right-col">
                    <div class="card">
                        <div class="card-header">
                            <h3>Quick Access</h3>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <button onclick="document.querySelector('[data-tab=\'children\']').click()" style="width:100%; padding: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; font-weight: 600; cursor: pointer; text-align: left; display: flex; align-items: center; gap: 12px; transition: all 0.3s;">
                                <i class="fas fa-baby" style="color: var(--primary);"></i> My Children
                            </button>
                            <button onclick="document.querySelector('[data-tab=\'activities\']').click()" style="width:100%; padding: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; font-weight: 600; cursor: pointer; text-align: left; display: flex; align-items: center; gap: 12px; transition: all 0.3s;">
                                <i class="fas fa-book-open" style="color: #10b981;"></i> Daily Activities
                            </button>
                            <button onclick="document.querySelector('[data-tab=\'billing\']').click()" style="width:100%; padding: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; font-weight: 600; cursor: pointer; text-align: left; display: flex; align-items: center; gap: 12px; transition: all 0.3s;">
                                <i class="fas fa-file-invoice-dollar" style="color: #f59e0b;"></i> Billing & Invoices
                            </button>
                        </div>
                    </div>
                    
                    <div class="card" style="background: #eff6ff; border-color: #bfdbfe;">
                        <h4 style="color: #1d4ed8; margin-bottom: 10px;"><i class="fas fa-lightbulb"></i> Tips for Parents</h4>
                        <p style="font-size: 0.85rem; color: #1e40af; line-height: 1.6;">You can update your contact information in the Settings tab to ensure you receive all notifications.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Children Tab -->
        <div id="children-tab" class="tab-content <?php echo $tab == 'children' ? 'active' : ''; ?>">
            <div class="registration-cta" style="background: linear-gradient(135deg, var(--primary) 0%, #00ACC1 100%); padding: 2rem; border-radius: 24px; color: white; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; box-shadow: 0 10px 20px rgba(38, 198, 218, 0.2);">
                <div>
                    <h2 style="margin-bottom: 8px;"><i class="fas fa-plus-circle"></i> Register a New Child</h2>
                    <p style="opacity: 0.9;">Add another child to your account to track their progress.</p>
                </div>
                <a href="add_child.php" class="btn" style="background: white; color: var(--primary); padding: 12px 30px; border-radius: 12px; text-decoration: none; font-weight: 700; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">Get Started</a>
            </div>

            <div class="card">
                <div class="section-header">
                    <h2>My Children</h2>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
                    <?php if(empty($children)): ?>
                        <div style="grid-column: 1/-1; text-align: center; padding: 4rem;">
                            <i class="fas fa-baby" style="font-size: 4rem; color: #e2e8f0; margin-bottom: 1rem;"></i>
                            <h3>No children linked.</h3>
                            <p style="color: #64748b;">Start by adding your first child to the system!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($children as $child): ?>
                            <div class="child-card">
                                <div class="child-avatar">
                                    <?php echo strtoupper(substr($child['name'], 0, 1)); ?>
                                </div>
                                <div class="child-info" style="flex: 1;">
                                    <h4 style="font-size: 1.2rem; margin-bottom: 5px;"><?php echo $child['name']; ?></h4>
                                    <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 12px;">ID: #C-<?php echo $child['id']; ?></p>
                                    <div style="display: flex; gap: 8px;">
                                        <button onclick="showChildProfile(<?php echo htmlspecialchars(json_encode($child)); ?>)" class="btn-action" style="padding: 6px 12px; background: #f1f5f9; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.8rem;"><i class="fas fa-eye"></i> View</button>
                                        <a href="edit_child.php?id=<?php echo $child['id']; ?>" class="btn-action" style="padding: 6px 12px; background: #eff6ff; color: #1d4ed8; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.8rem;"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $child['id']; ?>)" class="btn-action" style="padding: 6px 12px; background: #fef2f2; color: #ef4444; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.8rem;"><i class="fas fa-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>



        <!-- Activities Tab -->
        <div id="activities-tab" class="tab-content <?php echo $tab == 'activities' ? 'active' : ''; ?>">
            <div class="card">
                <div class="section-header">
                    <h2>Daily Activities History</h2>
                    <p style="color: #64748b; font-size: 0.9rem;">Review your children's growth and daily updates</p>
                </div>
                
                <div class="activities-stream" style="display: flex; flex-direction: column; gap: 2rem; margin-top: 2rem;">
                    <?php 
                    if(!empty($children)) {
                        $cids = array_column($children, 'id');
                        $cid_list = implode(',', $cids);
                        $full_act_query = "SELECT da.*, c.name as child_name FROM daily_activities da 
                                          JOIN children c ON da.child_id = c.id 
                                          WHERE da.child_id IN ($cid_list) 
                                          ORDER BY da.activity_date DESC, da.id DESC";
                        $full_act_res = mysqli_query($con, $full_act_query);
                        
                        if($full_act_res && mysqli_num_rows($full_act_res) > 0) {
                            while($act = mysqli_fetch_assoc($full_act_res)) {
                                ?>
                                <div class="activity-log-card" style="background: #f8fafc; border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; transition: transform 0.3s;">
                                    <div style="background: var(--secondary); color: white; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center;">
                                        <h4 style="margin: 0;"><i class="fas fa-child"></i> <?php echo $act['child_name']; ?></h4>
                                        <span style="font-weight: 600; opacity: 0.9;"><i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($act['activity_date'])); ?></span>
                                    </div>
                                    <div style="padding: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                        <div>
                                            <div style="margin-bottom: 20px;">
                                                <h5 style="color: #64748b; text-transform: uppercase; font-size: 0.75rem; margin-bottom: 8px; letter-spacing: 1px;">General Mood</h5>
                                                <?php 
                                                    $m_cl = '#166534'; $m_bg = '#dcfce7'; $m_ic = '😊';
                                                    if($act['mood'] == 'Fussy') { $m_cl = '#991b1b'; $m_bg = '#fee2e2'; $m_ic = '😢'; }
                                                    elseif($act['mood'] == 'Calm') { $m_cl = '#1e40af'; $m_bg = '#dbeafe'; $m_ic = '😐'; }
                                                    elseif($act['mood'] == 'Excited') { $m_cl = '#854d0e'; $m_bg = '#fef3c7'; $m_ic = '🤩'; }
                                                ?>
                                                <span class="badge" style="background: <?php echo $m_bg; ?>; color: <?php echo $m_cl; ?>; padding: 8px 16px; font-size: 1rem;">
                                                    <?php echo $m_ic . ' ' . $act['mood']; ?>
                                                </span>
                                            </div>
                                            <div style="margin-bottom: 20px;">
                                                <h5 style="color: #64748b; text-transform: uppercase; font-size: 0.75rem; margin-bottom: 8px; letter-spacing: 1px;">Nutrition & Meals</h5>
                                                <p style="margin: 0; line-height: 1.6; font-weight: 500;"><?php echo $act['meal_details'] ?: 'No meal details recorded.'; ?></p>
                                            </div>
                                            <div>
                                                <h5 style="color: #64748b; text-transform: uppercase; font-size: 0.75rem; margin-bottom: 8px; letter-spacing: 1px;">Nap / Rest Time</h5>
                                                <p style="margin: 0; line-height: 1.6; font-weight: 500;"><i class="fas fa-moon" style="color: #6366f1;"></i> <?php echo $act['nap_details'] ?: 'Not recorded.'; ?></p>
                                            </div>
                                        </div>
                                        <div style="border-left: 1px solid #e2e8f0; padding-left: 2rem;">
                                            <div style="margin-bottom: 20px;">
                                                <h5 style="color: #64748b; text-transform: uppercase; font-size: 0.75rem; margin-bottom: 8px; letter-spacing: 1px;">Activities Participated</h5>
                                                <p style="margin: 0; line-height: 1.6; font-weight: 500;"><?php echo $act['activities'] ?: 'No specific activities recorded.'; ?></p>
                                            </div>
                                            <div style="background: white; padding: 20px; border-radius: 16px; border: 1px dashed #cbd5e1;">
                                                <h5 style="color: var(--primary); text-transform: uppercase; font-size: 0.75rem; margin-bottom: 10px; letter-spacing: 1px;"><i class="fas fa-comment-dots"></i> Teacher's Note</h5>
                                                <p style="margin: 0; font-style: italic; color: #475569; line-height: 1.6;"><?php echo $act['notes'] ?: 'No additional notes.'; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<div style='text-align: center; padding: 5rem; background: #f8fafc; border-radius: 30px; color: #94a3b8;'><i class='fas fa-book-open' style='font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.5;'></i><h3>No Activity Logs Yet</h3><p>Your children's daily updates will appear here once recorded by the staff.</p></div>";
                        }
                    } else {
                        echo "<div style='text-align: center; padding: 5rem; background: #f8fafc; border-radius: 30px; color: #94a3b8;'><h3>No Children Linked</h3><p>Please link your children to see their activities.</p></div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Notifications Tab -->
        <div id="notifications-tab" class="tab-content <?php echo $tab == 'notifications' ? 'active' : ''; ?>">
            <!-- Attendance Section -->
            <div class="card" style="margin-bottom: 2rem; border-left: 4px solid #10b981;">
                <div class="section-header">
                    <h2><i class="fas fa-user-clock"></i> Daily Attendance Updates</h2>
                    <p style="color: #64748b; font-size: 0.9rem;">Arrival and departure logs for today: <?php echo date('d M Y'); ?></p>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
                    <?php if(!empty($children)): ?>
                        <?php foreach($children as $child): 
                            $c_id = $child['id'];
                            $today = date('Y-m-d');
                            $att_res = $con->query("SELECT * FROM attendance WHERE child_id = $c_id AND attendance_date = '$today'");
                            $att = $att_res->fetch_assoc();
                        ?>
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px; display: flex; align-items: center; gap: 15px;">
                                <div style="width: 45px; height: 45px; background: <?php echo $att ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo $att ? '#10b981' : '#ef4444'; ?>; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                    <i class="fas <?php echo $att ? 'fa-user-check' : 'fa-user-slash'; ?>"></i>
                                </div>
                                <div style="flex: 1;">
                                    <h4 style="margin: 0; font-size: 1rem; color: var(--secondary);"><?php echo $child['name']; ?></h4>
                                    <?php if($att): ?>
                                        <p style="margin: 5px 0 0; font-size: 0.85rem; color: #64748b;">
                                            <i class="fas fa-sign-in-alt"></i> In: <?php echo $att['check_in_time'] ? date('h:i A', strtotime($att['check_in_time'])) : '--:--'; ?> | 
                                            <i class="fas fa-sign-out-alt"></i> Out: <?php echo $att['check_out_time'] ? date('h:i A', strtotime($att['check_out_time'])) : '--:--'; ?>
                                        </p>
                                    <?php else: ?>
                                        <p style="margin: 5px 0 0; font-size: 0.85rem; color: #ef4444; font-weight: 600;">Status: Absent Today</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Events Section -->
            <div class="card">
                <div class="section-header">
                    <h2><i class="fas fa-bullhorn"></i> School Announcements & Events</h2>
                    <p style="color: #64748b; font-size: 0.9rem;">Important events and scheduled activities</p>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-top: 2rem;">
                    <?php 
                    $notif_query = "SELECT s.*, st.fullname as staff_name 
                                  FROM staff_schedule s
                                  JOIN users st ON s.staff_id = st.id
                                  ORDER BY s.activity_date DESC, s.start_time DESC";
                    $notif_res = mysqli_query($con, $notif_query);

                    if ($notif_res && mysqli_num_rows($notif_res) > 0):
                        while($notif = mysqli_fetch_assoc($notif_res)):
                            $is_today = $notif['activity_date'] == date('Y-m-d');
                    ?>
                        <div style="background: <?php echo $is_today ? '#f0f9ff' : '#f8fafc'; ?>; border: 1px solid <?php echo $is_today ? '#bae6fd' : '#e2e8f0'; ?>; border-radius: 20px; padding: 1.5rem; display: flex; gap: 20px; align-items: flex-start; transition: all 0.3s;">
                            <div style="width: 50px; height: 50px; background: <?php echo $is_today ? '#0ea5e9' : '#94a3b8'; ?>; color: white; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                                <i class="fas <?php echo $is_today ? 'fa-calendar-check' : 'fa-calendar-day'; ?>"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <h4 style="margin: 0; font-size: 1.1rem; color: var(--secondary);"><?php echo $notif['activity_name']; ?></h4>
                                    <span style="font-size: 0.8rem; font-weight: 600; color: #64748b;"><?php echo date('d M Y', strtotime($notif['activity_date'])); ?></span>
                                </div>
                                <p style="margin: 0 0 12px 0; color: #475569; font-size: 0.95rem;">
                                    Scheduled at <strong><?php echo $notif['room']; ?></strong> starting at <strong><?php echo date('h:i A', strtotime($notif['start_time'])); ?></strong>.
                                </p>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <span style="font-size: 0.8rem; color: #94a3b8;"><i class="fas fa-user-tie"></i> Posted by <?php echo $notif['staff_name']; ?></span>
                                    <span class="badge <?php echo $notif['status'] == 'Upcoming' ? 'badge-warning' : 'badge-success'; ?>" style="font-size: 0.75rem;"><?php echo $notif['status']; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <div style="text-align: center; padding: 5rem; color: #94a3b8;">
                            <i class="fas fa-bell-slash" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                            <h3>No Notifications</h3>
                            <p>You'll see updates here when staff members schedule new events.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Billing Tab -->
        <div id="billing-tab" class="tab-content <?php echo $tab == 'billing' ? 'active' : ''; ?>">
            <div class="card">
                <div class="section-header">
                    <h2>Billing & Invoices</h2>
                </div>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php 
                    if(!empty($children)) {
                        $billing_query = "SELECT b.*, c.name as child_name FROM billing b 
                                         JOIN children c ON b.child_id = c.id 
                                         WHERE c.parent_id = $user_id 
                                         ORDER BY b.id DESC";
                        $billing_res = mysqli_query($con, $billing_query);
                        if($billing_res && mysqli_num_rows($billing_res) > 0) {
                            while($bill = mysqli_fetch_assoc($billing_res)) {
                                $status_class = $bill['payment_status'] == 'Paid' ? 'badge-success' : 'badge-warning';
                                ?>
                                <div style="background: #f8fafc; padding: 2rem; border-radius: 20px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s;">
                                    <div style="display: flex; align-items: center; gap: 20px;">
                                        <div style="width: 60px; height: 60px; background: white; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--primary); box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <div>
                                            <h4 style="margin: 0; font-size: 1.1rem;">Invoice #INV-<?php echo $bill['id']; ?></h4>
                                            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.9rem;">For <?php echo $bill['child_name']; ?> - <?php echo $bill['monthly_attendance']; ?> Days</p>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <h3 style="margin: 0; color: var(--secondary);">Rs. <?php echo number_format($bill['total_monthly_fee'], 2); ?></h3>
                                        <div style="margin-top: 8px;">
                                            <span class="badge <?php echo $status_class; ?>" style="padding: 6px 15px;"><?php echo ucfirst($bill['payment_status']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<div style='text-align: center; padding: 4rem; background: #f8fafc; border-radius: 20px; color: #94a3b8;'><i class='fas fa-receipt' style='font-size: 3rem; margin-bottom: 1rem;'></i><p>No billing records found.</p></div>";
                        }
                    } else {
                        echo "<div style='text-align: center; padding: 4rem; background: #f8fafc; border-radius: 20px; color: #94a3b8;'>No children linked.</div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <div id="settings-tab" class="tab-content <?php echo $tab == 'settings' ? 'active' : ''; ?>">
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
                    <div id="settings-profile" class="settings-content active">
                        <div class="profile-card">
                            <div class="profile-header">
                                <div class="profile-avatar-large">
                                    <?php echo strtoupper(substr($fullname, 0, 1)); ?>
                                </div>
                                <div class="profile-info-header">
                                    <h4>My Profile</h4>
                                    <p>Manage your personal information and contact details.</p>
                                </div>
                            </div>

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

                            <form action="update_profile.php" method="POST">
                                <div style="display: flex; flex-direction: column; gap: 2rem;">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><i class="fas fa-user-tag"></i> Full Name</label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-user"></i>
                                                <input type="text" id="settings_fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" placeholder="Enter your full name" required>
                                            </div>
                                            <span class="error-text" id="fullname-error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fas fa-envelope"></i> Email Address</label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-at"></i>
                                                <input type="email" id="settings_email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" placeholder="Enter your email" required>
                                            </div>
                                            <span class="error-text" id="email-error"></span>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><i class="fas fa-phone-volume"></i> Phone Number</label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-phone"></i>
                                                <input type="text" id="settings_phone" name="phone" value="<?php echo htmlspecialchars($_SESSION['phone'] ?? ''); ?>" placeholder="Enter your phone number" maxlength="10" required>
                                            </div>
                                            <span class="error-text" id="phone-error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fas fa-user-shield"></i> Account Role</label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-shield"></i>
                                                <input type="text" value="Parent" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div style="padding-top: 1rem; border-top: 1px solid #f1f5f9; margin-top: 1rem;">
                                        <button type="submit" class="btn-save">
                                            <i class="fas fa-save"></i> Save Profile Changes
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id="settings-security" class="settings-content" style="display: none;">
                        <div class="profile-card">
                            <div class="profile-header">
                                <div class="profile-avatar-large" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="profile-info-header">
                                    <h4>Security Settings</h4>
                                    <p>Update your password to keep your account secure.</p>
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
                                <div style="display: flex; flex-direction: column; gap: 2rem;">
                                    <div class="form-group">
                                        <label><i class="fas fa-key"></i> Current Password</label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-lock-open"></i>
                                            <input type="password" id="old_password" name="old_password" placeholder="Enter current password" required>
                                        </div>
                                        <span class="error-text" id="old-password-error"></span>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><i class="fas fa-lock"></i> New Password</label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-shield-check"></i>
                                                <input type="password" name="new_password" id="new_password" placeholder="Minimum 4 characters" required>
                                            </div>
                                            <span class="error-text" id="new-password-error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fas fa-check-double"></i> Confirm New Password</label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-shield-halved"></i>
                                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Repeat new password" required>
                                            </div>
                                            <span class="error-text" id="confirm-password-error"></span>
                                        </div>
                                    </div>
                                    
                                    <div style="padding-top: 1rem; border-top: 1px solid #f1f5f9; margin-top: 1rem;">
                                        <button type="submit" class="btn-save" style="background: #4f46e5;">
                                            <i class="fas fa-shield-alt"></i> Update Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-switch to security tab if requested
        <?php if(isset($_GET['set_tab']) && $_GET['set_tab'] == 'security'): ?>
        document.addEventListener('DOMContentLoaded', () => {
            const securityTabBtn = document.querySelector('[data-settings-tab="security"]');
            if(securityTabBtn) securityTabBtn.click();
        });
        <?php endif; ?>

        // Settings Validation Patterns
        document.addEventListener('DOMContentLoaded', () => {
            const profileForm = document.querySelector('form[action="update_profile.php"]');
            const passwordForm = document.getElementById('passwordForm');

            // --- Shared Helper Functions ---
            const showError = (input, errorElement, message) => {
                if (message) {
                    input.classList.add('error');
                    errorElement.textContent = message;
                    errorElement.classList.add('show');
                } else {
                    input.classList.remove('error');
                    errorElement.classList.remove('show');
                    errorElement.textContent = "";
                }
            };

            // --- Profile Validations ---
            if (profileForm) {
                const fullnameInput = document.getElementById('settings_fullname');
                const phoneInput = document.getElementById('settings_phone');
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
                        showError(phoneInput, phoneError, "Phone number must be exactly 10 digits.");
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

            // --- Password Validations ---
            if (passwordForm) {
                const oldPassInput = document.getElementById('old_password');
                const newPassInput = document.getElementById('new_password');
                const confPassInput = document.getElementById('confirm_password');
                
                const oldPassErr = document.getElementById('old-password-error');
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
                        alert("Please ensure your new passwords match and are at least 4 characters long.");
                    }
                });
            }
        });
    </script>

    <!-- Child Profile Modal -->
    <div id="profileModal" class="profile-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeProfileModal()">&times;</span>
            <div style="text-align: center; margin-bottom: 2rem;">
                <div id="modalAvatar" class="child-avatar" style="width: 80px; height: 80px; font-size: 2rem; margin: 0 auto 15px;"></div>
                <h2 id="modalName" style="color: var(--secondary);"></h2>
                <p id="modalID" style="color: #64748b;"></p>
            </div>
            <div class="detail-row">
                <span class="detail-label">Age</span>
                <span id="modalAge" class="detail-value"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Gender</span>
                <span id="modalGender" class="detail-value"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Enrolled Date</span>
                <span id="modalEnrollment" class="detail-value"></span>
            </div>
            <div style="margin-top: 2rem; text-align: center;">
                <button onclick="closeProfileModal()" style="padding: 12px 30px; background: #f1f5f9; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;">Close Details</button>
            </div>
        </div>
    </div>

    <script src="parent_dashboard.js"></script>
    <script>
        function showChildProfile(child) {
            document.getElementById('modalAvatar').textContent = child.name.charAt(0);
            document.getElementById('modalName').textContent = child.name;
            document.getElementById('modalID').textContent = 'Student ID: #C-' + child.id;
            document.getElementById('modalAge').textContent = child.age + ' Years';
            document.getElementById('modalGender').textContent = child.gender.charAt(0).toUpperCase() + child.gender.slice(1);
            document.getElementById('modalEnrollment').textContent = new Date(child.enrolled_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
            
            document.getElementById('profileModal').style.display = 'flex';
        }

        function closeProfileModal() {
            document.getElementById('profileModal').style.display = 'none';
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to remove this child from your account? This will permanently delete the record.')) {
                window.location.href = 'delete_child.php?id=' + id;
            }
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('profileModal')) {
                closeProfileModal();
            }
        }
    </script>
</body>
</html>
