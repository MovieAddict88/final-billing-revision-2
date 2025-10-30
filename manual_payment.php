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
    header('Location: index.php');
    exit();
}

$customer_id = $_GET['customer'];
$customer = $admins->getCustomerInfo($customer_id);
$unpaid_bills = $admins->fetchAllIndividualBill($customer_id) ?: []; // ensure iterable even on query failure

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employer_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $reference_number = $_POST['reference_number'];
    $payment_method = $_POST['payment_method'];
    $screenshot = isset($_FILES['screenshot']) ? $_FILES['screenshot'] : null;
    $selected_bills = isset($_POST['bills']) ? $_POST['bills'] : [];
    $payment_date = $_POST['payment_date'];
    $payment_time = $_POST['payment_time'];

    if (!empty($selected_bills)) {
        if ($admins->processManualPayment($customer_id, $employer_id, $amount, $reference_number, $selected_bills, $payment_method, $screenshot, $payment_date, $payment_time)) {
            echo "<script>alert('Payment submitted successfully and is pending approval.'); window.close();</script>";
            exit();
        } else {
            $error_message = "Failed to process payment. Please try again.";
        }
    } else {
        $error_message = "Please select at least one bill to pay.";
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h3>Manual Payment for <?php echo htmlspecialchars($customer->full_name); ?></h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="customer" value="<?php echo $customer_id; ?>">
                        <h4>Open Balances</h4>
                        <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Month</th>
                                    <th>Amount</th>
                                    <th>Balance Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($unpaid_bills as $bill): ?>
                                    <?php
                                        // Compute the real due amount. Some unpaid bills store NULL/0 in balance.
                                        $has_positive_balance = is_numeric($bill->balance) && (float)$bill->balance > 0;
                                        $due_amount = $has_positive_balance ? (float)$bill->balance : (float)$bill->amount;
                                        $is_pending = ($bill->status === 'Pending');

                                        // Skip rows that are fully paid or otherwise have no amount due.
                                        if ($bill->status === 'Paid' || $due_amount <= 0) {
                                            continue;
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="bills[]" value="<?php echo $bill->id; ?>" data-balance="<?php echo htmlspecialchars(number_format($due_amount, 2, '.', '')); ?>" <?php echo $is_pending ? 'disabled' : ''; ?>>
                                        </td>
                                        <td><?php echo htmlspecialchars($bill->r_month); ?><?php if ($is_pending): ?> <span class="badge badge-warning">Pending approval</span><?php endif; ?></td>
                                        <td><?php echo htmlspecialchars(number_format((float)$bill->amount, 2)); ?></td>
                                        <td><?php echo htmlspecialchars(number_format($due_amount, 2)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                        <div class="form-group">
                            <label for="amount">Payment Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0" placeholder="Auto-fills with sum of selected balances">
                            <small class="form-text text-muted">Selecting bills will auto-fill the remaining balance. You may enter a lower initial amount if needed.</small>
                        </div>
                        <div class="form-group">
                            <label for="payment_date">Payment Date</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_time">Payment Time</label>
                            <input type="time" name="payment_time" id="payment_time" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="Manual">Manual</option>
                                <option value="GCash">GCash</option>
                                <option value="PayMaya">PayMaya</option>
                                <option value="Coins.ph">Coins.ph</option>
                            </select>
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


<?php
require_once 'includes/customer_footer.php';
?>
<script>
    (function() {
        var checkboxes = document.querySelectorAll('input[type="checkbox"][name="bills[]"]:not([disabled])');
        var amountInput = document.getElementById('amount');

        function updateAmountFromSelection() {
            var total = 0;
            checkboxes.forEach(function(cb) {
                if (cb.checked) {
                    var bal = parseFloat(cb.getAttribute('data-balance')) || 0;
                    total += bal;
                }
            });
            if (total > 0) {
                amountInput.value = total.toFixed(2);
                amountInput.max = total.toFixed(2);
            } else {
                amountInput.removeAttribute('max');
            }
        }

        checkboxes.forEach(function(cb) {
            cb.addEventListener('change', updateAmountFromSelection);
        });

        // If exactly one unpaid bill, pre-select it and prefill amount with its remaining balance
        var selectable = Array.prototype.slice.call(checkboxes);
        if (selectable.length === 1) {
            selectable[0].checked = true;
            updateAmountFromSelection();
        }
    })();
</script>