<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/classes/admin-class.php';

$admins = new Admins($dbh);

if (!isset($_SESSION['admin_session'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: bills.php');
    exit();
}

$payment_id = $_GET['id'];
$payment = $admins->getPaymentById($payment_id);
$customer = $admins->getCustomerInfo($payment->customer_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $paid_at = $payment->p_date ?: null;
        if ($admins->approvePayment($payment_id, $paid_at)) {
            echo "<script>window.opener.location.reload(); window.close();</script>";
            exit();
        } else {
            $error_message = "Failed to approve payment.";
        }
    } elseif (isset($_POST['reject'])) {
        if ($admins->rejectPayment($payment_id)) {
            echo "<script>window.opener.location.reload(); window.close();</script>";
            exit();
        } else {
            $error_message = "Failed to reject payment.";
        }
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h3>Payment Details</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <h4>Customer Information</h4>
                    <p><strong>Name:</strong> <?php echo $customer->full_name; ?></p>
                    <p><strong>Email:</strong> <?php echo $customer->email; ?></p>
                    <p><strong>Contact:</strong> <?php echo $customer->contact; ?></p>
                    <hr>
                    <h4>Payment Information</h4>
                    <p><strong>Month:</strong> <?php echo $payment->r_month; ?></p>
                    <?php
                        $submitted_amount = null;
                        if (is_numeric($payment->gcash_name)) {
                            $submitted_amount = (float)$payment->gcash_name;
                        }
                    ?>
                    <?php if ($submitted_amount !== null): ?>
                        <p><strong>Submitted Amount:</strong> <?php echo number_format($submitted_amount, 2); ?></p>
                    <?php endif; ?>
                    <p><strong>Total Paid So Far:</strong> <?php echo number_format((float)($payment->amount - $payment->balance), 2); ?></p>
                    <p><strong>New Balance:</strong> <?php echo number_format((float)$payment->balance, 2); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo $payment->payment_method; ?></p>
                    <?php if (in_array($payment->payment_method, ['GCash','PayMaya']) && !empty($payment->gcash_number)):
                        $wallet = json_decode($payment->gcash_number, true);
                        $wallet_name = is_array($wallet) && isset($wallet['name']) ? $wallet['name'] : null;
                        $wallet_number = is_array($wallet) && isset($wallet['number']) ? $wallet['number'] : null;
                        if (!$wallet_name && !$wallet_number) { // fallback for legacy plain string
                            $wallet_name = null;
                            $wallet_number = $payment->gcash_number;
                        }
                    ?>
                        <?php if ($wallet_name): ?><p><strong>Account Name:</strong> <?php echo htmlspecialchars($wallet_name); ?></p><?php endif; ?>
                        <?php if ($wallet_number): ?><p><strong>Account Number:</strong> <?php echo htmlspecialchars($wallet_number); ?></p><?php endif; ?>
                    <?php endif; ?>
                    <?php if ($payment->payment_method === 'Manual' && !empty($payment->employer_id)):
                        $employer_name = $admins->getEmployerNameById($payment->employer_id);
                        if ($employer_name): ?>
                            <p><strong>Paid by Employer:</strong> <?php echo htmlspecialchars($employer_name); ?></p>
                        <?php endif;
                    endif; ?>
                    <p><strong>Reference Number:</strong> <?php echo $payment->reference_number; ?></p>
                    <?php if ($payment->screenshot && file_exists($payment->screenshot)): ?>
                        <p><strong>Screenshot:</strong></p>
                        <img src="<?php echo $payment->screenshot; ?>" alt="Transaction Screenshot" class="img-fluid">
                    <?php endif; ?>
                    <hr>
                    <h4>Ledger Entries for this Bill</h4>
                    <?php
                        $rows = $dbh->prepare("SELECT h.*, pkg.name AS package_name, u.full_name AS employer_name FROM payment_history h LEFT JOIN packages pkg ON h.package_id = pkg.id LEFT JOIN kp_user u ON h.employer_id = u.user_id WHERE h.payment_id = ? ORDER BY h.paid_at ASC, h.id ASC");
                        $rows->execute([$payment_id]);
                        $history = $rows->fetchAll();
                    ?>
                    <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Paid</th>
                                <th>Balance After</th>
                                <th>Method</th>
                                <th>Employer</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($history): foreach ($history as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date('Y-m-d h:i:s A', strtotime($row->paid_at))); ?></td>
                                <td><?php echo number_format((float)$row->paid_amount, 2); ?></td>
                                <td><?php echo number_format((float)$row->balance_after, 2); ?></td>
                                <td><?php echo htmlspecialchars($row->payment_method ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row->employer_name ?: 'Admin'); ?></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="5" class="text-center">No ledger entries yet.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                    <hr>
                    <form action="" method="POST">
                        <button type="submit" name="approve" class="btn btn-success">Approve</button>
                        <button type="submit" name="reject" class="btn btn-danger">Reject</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>