<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'finance')) {
    header("Location: ../login/login.php");
    exit();
}

$message = '';
$billing = null;

// Get billing details - using 'billing_id'
if (isset($_GET['id'])) {
    $billing_id = mysqli_real_escape_string($con, $_GET['id']);
    $query = "SELECT * FROM billing WHERE billing_id = '$billing_id'";
    $result = mysqli_query($con, $query);
    $billing = mysqli_fetch_assoc($result);
}

// Process payment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $billing_id = $_POST['billing_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = $_POST['transaction_id'];
    $notes = $_POST['notes'];
    $payment_date = date('Y-m-d');
    
    // Get child_id from billing
    $get_child = "SELECT child_id FROM billing WHERE billing_id = '$billing_id'";
    $child_result = mysqli_query($con, $get_child);
    $child_data = mysqli_fetch_assoc($child_result);
    $child_id = $child_data['child_id'];
    
    // Record payment
    $insert_payment = "INSERT INTO payments (billing_id, child_id, amount, payment_date, payment_method, transaction_id, notes) 
                       VALUES ('$billing_id', '$child_id', '$amount', '$payment_date', '$payment_method', '$transaction_id', '$notes')";
    
    if (mysqli_query($con, $insert_payment)) {
        // Update billing payment status
        $update_billing = "UPDATE billing SET payment_status = 'paid', payment_date = '$payment_date' 
                           WHERE billing_id = '$billing_id'";
        mysqli_query($con, $update_billing);
        
        // Create or update invoice
        $invoice_number = "INV-" . date('Ymd') . "-" . $billing_id;
        $issue_date = date('Y-m-d');
        $due_date = date('Y-m-d', strtotime('+30 days'));
        
        $check_invoice = "SELECT * FROM invoices WHERE billing_id = '$billing_id'";
        $inv_check = mysqli_query($con, $check_invoice);
        
        if (mysqli_num_rows($inv_check) > 0) {
            $update_invoice = "UPDATE invoices SET status = 'paid', paid_date = '$payment_date', payment_method = '$payment_method' 
                               WHERE billing_id = '$billing_id'";
            mysqli_query($con, $update_invoice);
        } else {
            $insert_invoice = "INSERT INTO invoices (billing_id, child_id, invoice_number, amount, status, issue_date, due_date, paid_date, payment_method) 
                               VALUES ('$billing_id', '$child_id', '$invoice_number', '$amount', 'paid', '$issue_date', '$due_date', '$payment_date', '$payment_method')";
            mysqli_query($con, $insert_invoice);
        }
        
        echo "<script>alert('Payment recorded successfully!'); window.location.href='invoices.php';</script>";
        exit();
    } else {
        $message = "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px;'>
                    ❌ Error: " . mysqli_error($con) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Payment | Little Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px;
        }
        .container { max-width: 800px; margin: 0 auto; }
        .card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .invoice-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .invoice-details h3 { color: #333; margin-bottom: 15px; }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .amount-due {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
            margin: 15px 0;
            text-align: center;
        }
        .form-group { margin-bottom: 20px; }
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
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-back {
            background: #6c757d;
            margin-top: 10px;
            display: block;
            text-align: center;
            text-decoration: none;
        }
        h2 { color: #333; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2><i class="fas fa-credit-card"></i> Process Payment</h2>
            
            <?php echo $message; ?>
            
            <?php if($billing): ?>
            <div class="invoice-details">
                <h3>Invoice Details</h3>
                <div class="detail-row">
                    <span>Billing ID:</span>
                    <strong><?php echo $billing['billing_id']; ?></strong>
                </div>
                <div class="detail-row">
                    <span>Child Name:</span>
                    <strong><?php echo $billing['name']; ?></strong>
                </div>
                <div class="detail-row">
                    <span>Age:</span>
                    <strong><?php echo $billing['age']; ?> years</strong>
                </div>
                <div class="detail-row">
                    <span>Monthly Fee:</span>
                    <strong>Rs. <?php echo number_format($billing['monthly_fee'], 2); ?></strong>
                </div>
                <div class="detail-row">
                    <span>Additional Fee:</span>
                    <strong>Rs. <?php echo number_format($billing['additional_fee'], 2); ?></strong>
                </div>
                <div class="detail-row">
                    <span>Extra Hours Fee:</span>
                    <strong>Rs. <?php echo number_format($billing['extra_hours_fee'], 2); ?></strong>
                </div>
                <div class="amount-due">
                    Total Amount: Rs. <?php echo number_format($billing['total_monthly_fee'], 2); ?>
                </div>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="billing_id" value="<?php echo $billing['billing_id']; ?>">
                <input type="hidden" name="amount" value="<?php echo $billing['total_monthly_fee']; ?>">
                
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" required>
                        <option value="">Select Payment Method</option>
                        <option value="cash">Cash</option>
                        <option value="card">Credit/Debit Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Transaction ID / Reference</label>
                    <input type="text" name="transaction_id" placeholder="Enter transaction ID or reference number">
                </div>
                
                <div class="form-group">
                    <label>Payment Notes</label>
                    <textarea name="notes" rows="3" placeholder="Any additional notes..."></textarea>
                </div>
                
                <button type="submit" class="btn"><i class="fas fa-check-circle"></i> Confirm Payment</button>
                <a href="invoices.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Cancel</a>
            </form>
            <?php else: ?>
            <p style="text-align: center; color: red;">Invoice not found!</p>
            <a href="invoices.php" class="btn btn-back">Back to Invoices</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>