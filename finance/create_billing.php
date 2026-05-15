<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'finance')) {
    header("Location: ../login/login.php");
    exit();
}

// Function to calculate daily rate based on age
function getDailyRate($age) {
    if ($age >= 1 && $age <= 5) {
        return 900;
    } elseif ($age >= 6 && $age <= 12) {
        return 600;
    } elseif ($age >= 13 && $age <= 17) {
        return 500;
    }
    return 0;
}

// Function to calculate extra hours fee based on hours
function calculateExtraHoursFee($extra_hours) {
    if ($extra_hours <= 0) return 0;
    
    $fee = 0;
    $remaining = $extra_hours;
    
    // First slab: 1-6 hours @ Rs.400 per hour
    if ($remaining > 0) {
        $first = min($remaining, 6);
        $fee += $first * 400;
        $remaining -= $first;
    }
    
    // Second slab: 7-14 hours @ Rs.300 per hour
    if ($remaining > 0) {
        $second = min($remaining, 8);
        $fee += $second * 300;
        $remaining -= $second;
    }
    
    // Third slab: 15+ hours @ Rs.200 per hour
    if ($remaining > 0) {
        $fee += $remaining * 200;
    }
    
    return $fee;
}

$child_data = null;
$calculated_fees = null;
$child_id = '';
$error_message = '';

// AJAX endpoint to get child details
if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_child' && isset($_GET['child_id'])) {
    header('Content-Type: application/json');
    $child_id = mysqli_real_escape_string($con, $_GET['child_id']);
    $query = "SELECT id, name, age FROM children WHERE id = '$child_id'";
    $result = mysqli_query($con, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Child not found']);
    }
    exit();
}

// Calculate fees form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calculate'])) {
    $child_id = $_POST['child_id'];
    $age = $_POST['age'];
    $month = $_POST['month'];
    $monthly_attendance = $_POST['monthly_attendance'];
    $weekend_attendance = $_POST['weekend_attendance'];
    $extra_hours = $_POST['extra_hours'];
    $notes = $_POST['notes'];
    
    $daily_rate = getDailyRate($age);
    $monthly_fee = $monthly_attendance * $daily_rate;
    $additional_fee = $weekend_attendance * 1000;
    $extra_hours_fee = calculateExtraHoursFee($extra_hours);
    $total = $monthly_fee + $additional_fee + $extra_hours_fee;
    
    $calculated_fees = [
        'age' => $age,
        'daily_rate' => $daily_rate,
        'monthly_fee' => $monthly_fee,
        'additional_fee' => $additional_fee,
        'extra_hours_fee' => $extra_hours_fee,
        'total' => $total,
        'month' => $month,
        'monthly_attendance' => $monthly_attendance,
        'weekend_attendance' => $weekend_attendance,
        'extra_hours' => $extra_hours,
        'notes' => $notes,
        'child_name' => $_POST['child_name'],
        'child_id' => $child_id
    ];
}

// Save billing to database
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
    $child_id = $_POST['child_id'];
    $name = $_POST['child_name'];
    $age = $_POST['age'];
    $month = $_POST['month'];
    $monthly_attendance = $_POST['monthly_attendance'];
    $monthly_fee = $_POST['monthly_fee'];
    $weekend_attendance = $_POST['weekend_attendance'];
    $additional_fee = $_POST['additional_fee'];
    $extra_hours = $_POST['extra_hours'];
    $extra_hours_fee = $_POST['extra_hours_fee'];
    $total = $_POST['total'];
    $notes = $_POST['notes'];
    $payment_status = 'pending';
    
    $insert = "INSERT INTO billing (child_id, name, age, monthly_attendance, monthly_fee, 
               weekend_attendance, additional_fee, extra_hours, extra_hours_fee, total_monthly_fee, payment_status, notes) 
               VALUES ('$child_id', '$name', '$age', '$monthly_attendance', '$monthly_fee', 
               '$weekend_attendance', '$additional_fee', '$extra_hours', '$extra_hours_fee', 
               '$total', '$payment_status', '$notes')";
    
    if (mysqli_query($con, $insert)) {
        $billing_id = mysqli_insert_id($con);
        
        // Generate invoice number
        $invoice_number = "INV-" . date('Ymd') . "-" . $billing_id;
        $issue_date = date('Y-m-d');
        $due_date = date('Y-m-d', strtotime('+30 days'));
        
        $invoice_insert = "INSERT INTO invoices (billing_id, child_id, invoice_number, amount, issue_date, due_date, status) 
                           VALUES ('$billing_id', '$child_id', '$invoice_number', '$total', '$issue_date', '$due_date', 'unpaid')";
        
        if (mysqli_query($con, $invoice_insert)) {
            echo "<script>
                alert('Billing created successfully! Invoice: $invoice_number');
                window.location.href='invoices.php';
            </script>";
            exit();
        } else {
            $error_message = "Billing saved but invoice error: " . mysqli_error($con);
        }
    } else {
        $error_message = "Error: " . mysqli_error($con);
    }
    if($error_message) {
        echo "<script>alert('$error_message');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Child Billing | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }
        
        /* Sidebar Styles matching your dashboard */
        .sidebar {
            width: 280px;
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100%;
            padding: 30px;
        }
        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-links {
            list-style: none;
        }
        .nav-links li {
            margin-bottom: 15px;
        }
        .nav-links a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .nav-links a:hover, .nav-links a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        /* Header */
        .header {
            background: white;
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 {
            color: #2c3e50;
            font-size: 1.8rem;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logout-btn {
            background: #ef4444;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .logout-btn:hover {
            background: #dc2626;
        }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
            padding-left: 15px;
            font-size: 1.3rem;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        input[readonly] {
            background: #f8f9fa;
            cursor: not-allowed;
            border-color: #e0e0e0;
        }
        
        .row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        /* Buttons */
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        .btn-success {
            background: #27ae60;
            color: white;
        }
        .btn-success:hover {
            background: #219a52;
            transform: translateY(-2px);
        }
        
        /* Fees Summary */
        .fees-summary {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-top: 20px;
        }
        .fee-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .fee-total {
            font-size: 1.3rem;
            font-weight: bold;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid white;
        }
        
        .alert-info {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            border-left: 4px solid #27ae60;
        }
        
        .child-error {
            color: #e74c3c;
            margin-top: 10px;
            padding: 10px;
            background: #fadbd8;
            border-radius: 8px;
            display: none;
        }
        
        .guide-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .guide-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .guide-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .guide-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
        }
        .guide-card h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .guide-card ul {
            margin-left: 20px;
        }
        .guide-card li {
            margin: 8px 0;
            color: #555;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar matching your dashboard -->
    <div class="sidebar">
        <h2><i class="fas fa-hands-holding-child"></i> Little Haven</h2>
        <ul class="nav-links">
            <li><a href="finance_dashboard.php"><i class="fas fa-chart-pie"></i> Overview</a></li>
            <li><a href="create_billing.php" class="active"><i class="fas fa-plus-circle"></i> Create Billing</a></li>
            <li><a href="invoices.php"><i class="fas fa-file-invoice"></i> Invoices</a></li>
            <li><a href="payments.php"><i class="fas fa-receipt"></i> Payments</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1><i class="fas fa-calculator"></i> Create Child Billing</h1>
            <div class="user-info">
                <span>Welcome, <strong><?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Finance Manager'; ?></strong></span>
                <a href="../login/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <!-- Child Information Card -->
        <div class="card">
            <h2><i class="fas fa-child"></i> Child Information</h2>
            <div class="row">
                <div class="form-group">
                    <label>Enter Child ID <span style="color: red;">*</span></label>
                    <input type="number" id="child_id" name="child_id" placeholder="Enter Child ID" autocomplete="off">
                    <small style="color: #6c757d;">Enter child ID and press Tab or click outside</small>
                </div>
                <div class="form-group">
                    <label>Child Name</label>
                    <input type="text" id="child_name" readonly placeholder="Auto-filled from database">
                </div>
                <div class="form-group">
                    <label>Age (Years)</label>
                    <input type="text" id="age" readonly placeholder="Auto-filled from database">
                </div>
            </div>
            <div id="child_error" class="child-error"></div>
        </div>
        
        <!-- Calculation Section (Hidden until child is selected) -->
        <div id="calculation_section" style="display: none;">
            <div class="card">
                <h2><i class="fas fa-chart-line"></i> Fee Calculation</h2>
                <form method="POST" action="" id="fee_form">
                    <input type="hidden" id="form_child_id" name="child_id">
                    <input type="hidden" id="form_child_name" name="child_name">
                    <input type="hidden" id="form_age" name="age">
                    
                    <div class="row">
                        <div class="form-group">
                            <label>Select Month</label>
                            <input type="month" name="month" value="<?php echo date('Y-m'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Monthly Attendance (Days)</label>
                            <input type="number" name="monthly_attendance" placeholder="Number of days attended" min="0" max="31" required>
                        </div>
                        <div class="form-group">
                            <label>Weekend Attendance (Days)</label>
                            <input type="number" name="weekend_attendance" placeholder="Number of weekend days" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>Extra Hours</label>
                            <input type="number" name="extra_hours" placeholder="Extra hours" min="0" step="0.5" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" rows="3" placeholder="Any additional notes..."></textarea>
                    </div>
                    
                    <button type="submit" name="calculate" class="btn btn-primary"><i class="fas fa-calculator"></i> Calculate Fees</button>
                </form>
            </div>
        </div>
        
        <!-- Fee Summary (Shown after calculation) -->
        <?php if($calculated_fees): ?>
        <div class="fees-summary">
            <h3 style="margin-bottom: 15px;"><i class="fas fa-receipt"></i> Fee Summary for <?php echo $calculated_fees['child_name']; ?></h3>
            <div class="fee-item">
                <span>📅 Monthly Attendance (<?php echo $calculated_fees['monthly_attendance']; ?> days @ Rs.<?php echo number_format($calculated_fees['daily_rate']); ?> per day)</span>
                <span>Rs. <?php echo number_format($calculated_fees['monthly_fee'], 2); ?></span>
            </div>
            <div class="fee-item">
                <span>🎯 Weekend Attendance (<?php echo $calculated_fees['weekend_attendance']; ?> days @ Rs.1000 per day)</span>
                <span>Rs. <?php echo number_format($calculated_fees['additional_fee'], 2); ?></span>
            </div>
            <div class="fee-item">
                <span>⏰ Extra Hours (<?php echo $calculated_fees['extra_hours']; ?> hours)</span>
                <span>Rs. <?php echo number_format($calculated_fees['extra_hours_fee'], 2); ?></span>
            </div>
            <div class="fee-total">
                <span><strong>💰 Total Monthly Fee</strong></span>
                <span><strong>Rs. <?php echo number_format($calculated_fees['total'], 2); ?></strong></span>
            </div>
            
            <form method="POST" action="" style="margin-top: 20px;">
                <input type="hidden" name="save" value="1">
                <input type="hidden" name="child_id" value="<?php echo $calculated_fees['child_id']; ?>">
                <input type="hidden" name="child_name" value="<?php echo $calculated_fees['child_name']; ?>">
                <input type="hidden" name="age" value="<?php echo $calculated_fees['age']; ?>">
                <input type="hidden" name="month" value="<?php echo $calculated_fees['month']; ?>">
                <input type="hidden" name="monthly_attendance" value="<?php echo $calculated_fees['monthly_attendance']; ?>">
                <input type="hidden" name="monthly_fee" value="<?php echo $calculated_fees['monthly_fee']; ?>">
                <input type="hidden" name="weekend_attendance" value="<?php echo $calculated_fees['weekend_attendance']; ?>">
                <input type="hidden" name="additional_fee" value="<?php echo $calculated_fees['additional_fee']; ?>">
                <input type="hidden" name="extra_hours" value="<?php echo $calculated_fees['extra_hours']; ?>">
                <input type="hidden" name="extra_hours_fee" value="<?php echo $calculated_fees['extra_hours_fee']; ?>">
                <input type="hidden" name="total" value="<?php echo $calculated_fees['total']; ?>">
                <input type="hidden" name="notes" value="<?php echo $calculated_fees['notes']; ?>">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save Billing & Generate Invoice</button>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Fee Calculation Guide -->
        <div class="guide-section">
            <h3><i class="fas fa-info-circle"></i> Fee Calculation Guide</h3>
            <div class="guide-grid">
                <div class="guide-card">
                    <h4>📊 Age-Based Daily Rates</h4>
                    <ul>
                        <li>Age 1-5 years: <strong>Rs. 900</strong> per day</li>
                        <li>Age 6-12 years: <strong>Rs. 600</strong> per day</li>
                        <li>Age 13-17 years: <strong>Rs. 500</strong> per day</li>
                    </ul>
                </div>
                <div class="guide-card">
                    <h4>💰 Extra Hours Rate (Slab System)</h4>
                    <ul>
                        <li>1-6 hours: <strong>Rs. 400/hour</strong></li>
                        <li>7-14 hours: <strong>Rs. 300/hour</strong></li>
                        <li>15+ hours: <strong>Rs. 200/hour</strong></li>
                    </ul>
                </div>
                <div class="guide-card">
                    <h4>🎯 Additional Fees</h4>
                    <ul>
                        <li>Weekend attendance: <strong>Rs. 1000/day</strong> (all ages)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        $('#child_id').on('blur', function() {
            var child_id = $(this).val();
            if(child_id) {
                $('#child_error').hide();
                $.ajax({
                    url: 'create_billing.php',
                    type: 'GET',
                    data: { ajax: 'get_child', child_id: child_id },
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            $('#child_name').val(response.data.name);
                            $('#age').val(response.data.age);
                            $('#form_child_id').val(response.data.id);
                            $('#form_child_name').val(response.data.name);
                            $('#form_age').val(response.data.age);
                            $('#calculation_section').slideDown();
                            $('#child_error').hide();
                        } else {
                            $('#child_name').val('');
                            $('#age').val('');
                            $('#form_child_id').val('');
                            $('#form_child_name').val('');
                            $('#form_age').val('');
                            $('#calculation_section').slideUp();
                            $('#child_error').html(response.message).show();
                        }
                    },
                    error: function() {
                        $('#child_error').html('Error fetching child data').show();
                    }
                });
            } else {
                $('#calculation_section').slideUp();
            }
        });
    });
    </script>

</body>

</html>