<?php
session_start();
require_once 'config/dbconnection.php';
require_once 'includes/classes/admin-class.php';

$dbh = new Dbconnect();
$admins = new Admins($dbh);

// Get user information from session
$user_role = $_SESSION['user_role'] ?? 'admin';
$user_id = $_SESSION['user_id'] ?? null;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$q = isset($_GET['q']) ? $_GET['q'] : null;

$offset = ($page - 1) * $limit;

// Fetch data based on user role
if ($user_role == 'admin') {
    // Admin can view all disconnected customers
    $results = $admins->fetchDisconnectedCustomersPage($offset, $limit, $q);
    $total_records = $admins->countDisconnectedCustomers($q);
} else {
    // Employers can view their own disconnected customers
    $results = $admins->fetchDisconnectedCustomersByEmployerPage($user_id, $offset, $limit, $q);
    $total_records = $admins->countDisconnectedCustomersByEmployer($user_id, $q);
}

$total_pages = ceil($total_records / $limit);
$packages = $admins->getPackages();

if (isset($results) && sizeof($results) > 0) {
    foreach ($results as $result) {
        $package_name = '';
        foreach ($packages as $package) {
            if ($package->id == $result->package_id) {
                $package_name = $package->name;
                break;
            }
        }
?>
        <tr>
            <td><?= $result->original_id ?></td>
            <td><?= $result->full_name ?></td>
            <td><?= $result->employer_name ?? 'N/A' ?></td>
            <td><?= $result->nid ?></td>
            <td><?= $result->address ?></td>
            <td><?= $package_name ?></td>
            <td><?= $result->ip_address ?></td>
            <td><?= $result->email ?></td>
            <td><?= $result->contact ?></td>
            <td><?= $result->conn_type ?></td>
            <td><span class="label label-danger"><?= $result->status ?></span></td>
            <td><?= $result->login_code ?></td>
            <td><?= $result->due_date ?></td>
            <td><?= $result->remarks ?></td>
            <td style="white-space: nowrap;"><?= date('Y-m-d H:i', strtotime($result->disconnected_at)) ?></td>
            <td>
                <?php
                $reconnection_request = $admins->getPendingReconnectionRequest($result->id);
                if ($reconnection_request) { ?>
                    <a href="view_reconnection_payment.php?id=<?= $reconnection_request->id ?>" class="btn btn-info">View Request</a>
                <?php } else { ?>
                    <a href="reconnection_payment.php?customer=<?= $result->id ?>" class="btn btn-warning">Request Reconnection</a>
                <?php } ?>
            </td>
        </tr>
    <?php }
} else { ?>
    <tr>
        <td colspan="16" align="center">No disconnected clients found</td>
    </tr>
<?php }

if ($total_pages > 1) { ?>
    <tr class="pagination-row" data-page="<?= $page ?>" data-total="<?= $total_records ?>" data-limit="<?= $limit ?>" data-query="<?= $q ?>">
        <td colspan="15" align="center">
            <ul class="pagination">
                <?php if ($page > 1) : ?>
                    <li><a href="#" class="page-prev">&laquo;</a></li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <li class="<?= ($i == $page) ? 'active' : '' ?>"><a href="#" class="page-num"><?= $i ?></a></li>
                <?php endfor; ?>
                <?php if ($page < $total_pages) : ?>
                    <li><a href="#" class="page-next">&raquo;</a></li>
                <?php endif; ?>
            </ul>
        </td>
    </tr>
<?php }
?>