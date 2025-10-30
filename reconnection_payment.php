<?php
session_start();
require_once 'config/dbconnection.php';
require_once 'includes/customer_header.php';
require_once 'includes/classes/admin-class.php';

$admins = new Admins($dbh);

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employer') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['customer'])) {
    header('Location: disconnected_clients.php');
    exit();
}

$customer_id = $_GET['customer'];
$customer = $admins->getDisconnectedCustomerInfo($customer_id);

if (!$customer) {
    header('Location: disconnected_clients.php');
    exit();
}

// Get package info to calculate reconnection fee
$package = $admins->getPackageInfo($customer->package_id);
$monthly_fee = $package ? (float)$package->fee : 0;

// Calculate overdue consumption based on the due date
$due_date = new DateTime($customer->due_date);
$today = new DateTime();

// Calculate overdue days, ensuring it's not negative
if ($due_date > $today) {
    $overdue_days = 0;
} else {
    $interval = $due_date->diff($today);
    $overdue_days = $interval->days;
}

// Calculate daily rate (standard 30-day billing cycle)
$days_in_month = 30;
$daily_rate = $monthly_fee / $days_in_month;

// Calculate overdue consumption for the period
$overdue_consumption = $overdue_days * $daily_rate;

// Get outstanding balance (remaining balance from previous bills)
$outstanding_balance = (float)$customer->balance;

// Calculate total due: outstanding balance + overdue consumption
$total_due = $outstanding_balance + $overdue_consumption;

// Minimum payment must cover at least the outstanding balance
$minimum_payment = $outstanding_balance;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employer_id = $_SESSION['user_id'];
    $amount = (float)$_POST['amount'];
    $reference_number = $_POST['reference_number'];
    $payment_method = $_POST['payment_method'];
    $screenshot = isset($_FILES['screenshot']) ? $_FILES['screenshot'] : null;
    $payment_date = $_POST['payment_date'];
    $payment_time = $_POST['payment_time'];
    
    // Validate payment amount
    if ($amount < $outstanding_balance) {
        $error_message = "Payment amount must be at least the outstanding balance of ₱" . number_format($outstanding_balance, 2);
    } elseif ($amount > ($total_due * 3)) {
        $error_message = "Payment amount seems too high. Please verify the amount.";
    } else {
        if ($admins->processReconnectionPayment($customer_id, $employer_id, $amount, $reference_number, $payment_method, $screenshot, $payment_date, $payment_time)) {
            $_SESSION['success'] = 'Reconnection request submitted successfully and is pending approval.';
            header('Location: disconnected_clients.php');
            exit();
        } else {
            $error_message = "Failed to process reconnection request. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reconnection Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .breakdown-card {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .total-due {
            background: #e7f3ff;
            border: 2px solid #007bff;
            border-radius: 8px;
        }
        .calculation-steps {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mt-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Reconnection Payment for <?php echo htmlspecialchars($customer->full_name); ?></h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <!-- Calculation Breakdown -->
                    <div class="calculation-steps">
                        <h5>Payment Calculation Breakdown:</h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Monthly Package:</span>
                            <strong>₱<?php echo number_format($monthly_fee, 2); ?></strong>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Daily Rate Calculation:</span>
                            <span>₱<?php echo number_format($monthly_fee, 2); ?> ÷ <?php echo $days_in_month; ?> days = <strong>₱<?php echo number_format($daily_rate, 2); ?>/day</strong></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Overdue Period:</span>
                            <span><?php echo $due_date->format('M j, Y'); ?> to <?php echo $today->format('M j, Y'); ?> <strong>(<?php echo $overdue_days; ?> days)</strong></span>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <span>Overdue Consumption:</span>
                            <span><?php echo $overdue_days; ?> days × ₱<?php echo number_format($daily_rate, 2); ?>/day = <strong>₱<?php echo number_format($overdue_consumption, 2); ?></strong></span>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="breakdown-card p-3 mb-4">
                        <div class="d-flex justify-content-between">
                            <span>Outstanding Balance:</span>
                            <strong>₱<?php echo number_format($outstanding_balance, 2); ?></strong>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <span>Overdue Consumption:</span>
                            <strong>₱<?php echo number_format($overdue_consumption, 2); ?></strong>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center total-due p-2">
                            <h5 class="mb-0"><strong>Total Amount Due:</strong></h5>
                            <h5 class="mb-0"><strong>₱<?php echo number_format($total_due, 2); ?></strong></h5>
                        </div>
                        
                        <?php if ($outstanding_balance > 0): ?>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <small class="text-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <strong>Minimum payment required:</strong> ₱<?php echo number_format($outstanding_balance, 2); ?> (to cover outstanding balance)
                                    </small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Payment Form -->
                    <form action="" method="POST" enctype="multipart/form-data" id="paymentForm">
                        <input type="hidden" name="customer" value="<?php echo $customer_id; ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="amount" class="form-label">Payment Amount *</label>
                                    <input type="number" name="amount" id="amount" class="form-control" 
                                           step="0.01" min="<?php echo $outstanding_balance; ?>" 
                                           max="<?php echo $total_due * 3; ?>" 
                                           value="<?php echo $total_due; ?>" required>
                                    <div class="form-text">
                                        <small>
                                            <strong>Minimum:</strong> ₱<?php echo number_format($outstanding_balance, 2); ?><br>
                                            <strong>Recommended:</strong> ₱<?php echo number_format($total_due, 2); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="payment_method" class="form-label">Payment Method *</label>
                                    <select name="payment_method" id="payment_method" class="form-control" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="GCash">GCash</option>
                                        <option value="PayMaya">PayMaya</option>
                                        <option value="Coins.ph">Coins.ph</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Cash">Cash</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="payment_date" class="form-label">Payment Date *</label>
                                    <input type="date" name="payment_date" id="payment_date" 
                                           class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="payment_time" class="form-label">Payment Time *</label>
                                    <input type="time" name="payment_time" id="payment_time" 
                                           class="form-control" value="<?php echo date('H:i'); ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="reference_number" class="form-label">Reference Number *</label>
                                    <input type="text" name="reference_number" id="reference_number" 
                                           class="form-control" placeholder="Enter transaction reference" required>
                                </div>
                            </div>
                        </div>

                        <!-- Wallet Account Fields (shown for e-wallet methods) -->
                        <div id="wallet_fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="wallet_account_name" class="form-label">Account Name *</label>
                                        <input type="text" name="wallet_account_name" id="wallet_account_name" 
                                               class="form-control" placeholder="Sender's account name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="wallet_account_number" class="form-label">Account Number *</label>
                                        <input type="text" name="wallet_account_number" id="wallet_account_number" 
                                               class="form-control" placeholder="Sender's account number">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="screenshot" class="form-label">Transaction Proof (Screenshot/Receipt)</label>
                            <input type="file" name="screenshot" id="screenshot" class="form-control" 
                                   accept="image/*,.pdf">
                            <div class="form-text">
                                <small>Upload proof of payment (screenshot of transaction or receipt photo)</small>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="disconnected_clients.php" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit Reconnection Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const paymentMethod = document.getElementById('payment_method');
    const walletFields = document.getElementById('wallet_fields');
    const walletAccountName = document.getElementById('wallet_account_name');
    const walletAccountNumber = document.getElementById('wallet_account_number');
    const paymentForm = document.getElementById('paymentForm');
    
    const outstandingBalance = <?php echo $outstanding_balance; ?>;
    const totalDue = <?php echo $total_due; ?>;

    // Amount validation
    amountInput.addEventListener('input', function() {
        const enteredAmount = parseFloat(this.value) || 0;
        
        if (enteredAmount < outstandingBalance) {
            this.setCustomValidity(`Payment must be at least ₱${outstandingBalance.toFixed(2)} to cover outstanding balance.`);
            this.classList.add('is-invalid');
        } else if (enteredAmount > totalDue * 3) {
            this.setCustomValidity('Payment amount seems too high. Please verify.');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });

    // Show/hide wallet fields based on payment method
    paymentMethod.addEventListener('change', function() {
        const ewalletMethods = ['GCash', 'PayMaya', 'Coins.ph'];
        
        if (ewalletMethods.includes(this.value)) {
            walletFields.style.display = 'block';
            walletAccountName.required = true;
            walletAccountNumber.required = true;
        } else {
            walletFields.style.display = 'none';
            walletAccountName.required = false;
            walletAccountNumber.required = false;
        }
    });

    // Form submission validation
    paymentForm.addEventListener('submit', function(e) {
        const enteredAmount = parseFloat(amountInput.value) || 0;
        
        if (enteredAmount < outstandingBalance) {
            e.preventDefault();
            alert(`Error: Payment amount must be at least ₱${outstandingBalance.toFixed(2)} to cover the outstanding balance.`);
            amountInput.focus();
            return false;
        }
        
        if (enteredAmount > totalDue * 3) {
            e.preventDefault();
            alert('Error: Payment amount seems too high. Please verify the amount.');
            amountInput.focus();
            return false;
        }

        // Confirm submission
        if (!confirm('Are you sure you want to submit this reconnection request?')) {
            e.preventDefault();
            return false;
        }
    });

    // Set default time to current time if not set
    if (!document.getElementById('payment_time').value) {
        document.getElementById('payment_time').value = '<?php echo date("H:i"); ?>';
    }

    // Trigger payment method change on page load
    paymentMethod.dispatchEvent(new Event('change'));
});
</script>
</body>
</html>