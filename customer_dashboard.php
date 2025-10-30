<?php
session_start();
require_once 'includes/customer_header.php';
require_once 'includes/classes/admin-class.php';

$admins = new Admins($dbh);

if (isset($_POST['login_code'])) {
    $login_code = $_POST['login_code'];
    $customer = $admins->fetchCustomerByLoginCode($login_code);
    if ($customer) {
        $_SESSION['customer_id'] = $customer->id;
    } else {
        header('Location: customer_login.php?error=invalid_code');
        exit();
    }
}

if (!isset($_SESSION['customer_id'])) {
    header('Location: customer_login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer = $admins->getCustomerInfo($customer_id);
$package = $admins->getPackageInfo($customer->package_id);
$payments = $admins->fetchAllIndividualBill($customer_id);
$ledger = $admins->fetchPaymentHistoryByCustomer($customer_id);

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Welcome, <?php echo $customer->full_name; ?></h3>
                    <a href="customer_logout.php" class="btn btn-danger">Logout</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Customer Details</h4>
                            <p><strong>Name:</strong> <?php echo $customer->full_name; ?></p>
                            <p><strong>Email:</strong> <?php echo $customer->email; ?></p>
                            <p><strong>Contact:</strong> <?php echo $customer->contact; ?></p>
                            <p><strong>Address:</strong> <?php echo $customer->address; ?></p>
                            <p><strong>Due Date:</strong> <?php echo $customer->due_date; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h4>Package Information</h4>
                            <p><strong>Package:</strong> <?php echo $package->name; ?></p>
                            <p><strong>Fee:</strong> <?php echo $package->fee; ?></p>
                        </div>
                    </div>
                    <hr>
                    <h4>Remarks</h4>
                    <p><?php echo htmlspecialchars($customer->remarks); ?></p>
                    <hr>
                    <h4>Payment History</h4>
                    <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Paid On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($payments): ?>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?php echo $payment->r_month; ?></td>
                                        <td><?php echo $payment->amount; ?></td>
                                        <td><?php echo number_format($payment->paid, 2); ?></td>
                                        <td><?php echo number_format($payment->balance, 2); ?></td>
                                        <td><?php
                                            if ($payment->status === 'Rejected') {
                                                echo 'Rejected';
                                            } elseif ($payment->paid > 0 && $payment->balance > 0) {
                                                echo 'Balance';
                                            } elseif ($payment->paid > 0 && $payment->balance == 0) {
                                                echo 'Paid';
                                            } else {
                                                echo 'Unpaid';
                                            }
                                        ?></td>
                                        <td><?= ($payment->payment_timestamp) ? date("F j, Y, g:i a", strtotime($payment->payment_timestamp)) : 'N/A' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No payment history found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                    <hr>
                    <h4>Invoice Payment Ledger</h4>
                    <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Billing Month</th>
                                <th>Package</th>
                                <th>Amount</th>
                                <th>Paid Amount</th>
                                <th>Balance</th>
                                <th>Payment Method</th>
                                <th>Reference Number</th>
                                <th>Employer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($ledger): ?>
                                <?php foreach ($ledger as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($row->paid_at))); ?></td>
                                        <td><?php echo htmlspecialchars($row->r_month); ?></td>
                                        <td><?php echo htmlspecialchars($row->package_name ?: 'N/A'); ?></td>
                                        <td><?php echo number_format((float)$row->amount, 2); ?></td>
                                        <td><?php echo number_format((float)$row->paid_amount, 2); ?></td>
                                        <td><?php echo number_format((float)$row->balance_after, 2); ?></td>
                                        <td><?php echo htmlspecialchars($row->payment_method ?: 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row->reference_number ?: 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row->employer_name ?: 'Admin'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No payment ledger yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/customer_footer.php';
?>