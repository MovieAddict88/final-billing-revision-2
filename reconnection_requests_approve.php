<?php
session_start();
require_once 'config/dbconnection.php';
require_once 'includes/classes/admin-class.php';

$dbh = new Dbconnect();
$admins = new Admins($dbh);

// This function will be created in a later step
$requests = $admins->fetchReconnectionRequests();

if (isset($requests) && sizeof($requests) > 0) {
    foreach ($requests as $request) {
?>
        <tr>
            <td><?= htmlspecialchars($request->customer_name) ?></td>
            <td><?= htmlspecialchars($request->employer_name) ?></td>
            <td><?= htmlspecialchars(number_format($request->amount, 2)) ?></td>
            <td><?= htmlspecialchars($request->payment_method) ?></td>
            <td><?= htmlspecialchars($request->reference_number) ?></td>
            <td><?= htmlspecialchars($request->payment_date) ?></td>
            <td>
                <a href="approve_reconnection.php?id=<?= $request->id ?>" class="btn btn-success">Approve</a>
                <a href="reject_reconnection.php?id=<?= $request->id ?>" class="btn btn-danger">Reject</a>
            </td>
        </tr>
    <?php }
} else { ?>
    <tr>
        <td colspan="7" align="center">No pending reconnection requests found</td>
    </tr>
<?php }
?>