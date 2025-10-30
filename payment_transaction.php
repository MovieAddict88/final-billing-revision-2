<?php
session_start();
require_once 'config/dbconnection.php';
require_once 'includes/customer_header.php';
require_once 'includes/classes/admin-class.php';

$admins = new Admins($dbh);

if (!isset($_SESSION['customer_id'])) {
    header('Location: customer_login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: customer_dashboard.php');
    exit();
}

$payment_id = $_GET['id'];
$payment = $admins->getPaymentById($payment_id); // Function exists in admin-class.php
$due_amount = ($payment && $payment->balance > 0) ? (float)$payment->balance : (float)$payment->amount;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$amount = $_POST['amount'];
$payment_method = $_POST['payment_method'];
$reference_number = $_POST['reference_number'];
// Store the submitted amount in gcash_name so admin can see this submission amount
// Retain wallet account number in gcash_number
$gcash_name = (is_numeric($amount) ? (float)$amount : null);
// Combine wallet account name and number into JSON for storage (backward compatible)
$wallet_account_name = isset($_POST['wallet_account_name']) ? trim($_POST['wallet_account_name']) : null;
$wallet_account_number = isset($_POST['wallet_account_number']) ? trim($_POST['wallet_account_number']) : (isset($_POST['gcash_number']) ? trim($_POST['gcash_number']) : null);
if (!empty($wallet_account_name) || !empty($wallet_account_number)) {
    $gcash_number = json_encode([
        'name' => $wallet_account_name ?: null,
        'number' => $wallet_account_number ?: null,
    ]);
} else {
    $gcash_number = null;
}
$screenshot = isset($_FILES['screenshot']) ? $_FILES['screenshot'] : null;

    if ($admins->processPayment($payment_id, $payment_method, $reference_number, $amount, $gcash_name, $gcash_number, $screenshot)) {
        header('Location: customer_dashboard.php?payment=success');
        exit();
    } else {
        $error_message = "Failed to process payment. Please try again.";
    }
}

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h3>Process Payment</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <h4>Payment for <?php echo htmlspecialchars($payment->r_month); ?></h4>
                    <p><strong>Remaining Due:</strong> <?php echo number_format((float)$due_amount, 2); ?></p>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="amount">Amount to Pay (Initial allowed)</label>
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0" max="<?php echo htmlspecialchars($due_amount); ?>" value="<?php echo htmlspecialchars(number_format((float)$due_amount, 2, '.', '')); ?>" required>
                            <small class="form-text text-muted">You can pay any amount up to the remaining balance.</small>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="GCash">GCash</option>
                                <option value="PayMaya">PayMaya</option>
                                <option value="Coins.ph">Coins.ph</option>
                            </select>
                        </div>
                        <div id="wallet_fields" style="display: none;">
                            <div class="form-group">
                                <label for="wallet_account_name">Account Name (GCash/PayMaya/Coins.ph)</label>
                                <input type="text" name="wallet_account_name" id="wallet_account_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="wallet_account_number">Account Number</label>
                                <input type="text" name="wallet_account_number" id="wallet_account_number" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reference_number">Reference Number</label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="screenshot">Transaction Screenshot</label>
                            <input type="file" name="screenshot" id="screenshot" class="form-control-file" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('payment_method').addEventListener('change', function () {
        var walletFields = document.getElementById('wallet_fields');
        if (this.value === 'GCash' || this.value === 'PayMaya' || this.value === 'Coins.ph') {
            walletFields.style.display = 'block';
            document.getElementById('wallet_account_name').required = true;
            document.getElementById('wallet_account_number').required = true;
        } else {
            walletFields.style.display = 'none';
            document.getElementById('wallet_account_name').required = false;
            document.getElementById('wallet_account_number').required = false;
        }
    });

    // Trigger the change event on page load to set the initial state
    document.getElementById('payment_method').dispatchEvent(new Event('change'));

    // Clamp amount input to remaining due
    (function(){
        var amountEl = document.getElementById('amount');
        if (!amountEl) return;
        var max = parseFloat(amountEl.getAttribute('max')) || 0;
        amountEl.addEventListener('input', function(){
            var val = parseFloat(this.value) || 0;
            if (val > max) this.value = max.toFixed(2);
            if (val < 0) this.value = '0.00';
        });
    })();
</script>

<?php
require_once 'includes/customer_footer.php';
?>