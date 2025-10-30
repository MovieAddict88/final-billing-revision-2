<?php
require_once 'includes/header.php';

if (!isset($_SESSION['admin_session'])) {
    $commons->redirectTo(SITE_PATH . 'login.php');
}

require_once 'config/dbconnection.php';
$dbh = new Dbconnect();
require_once "includes/classes/admin-class.php";
$admins = new Admins($dbh);

$user_role = $_SESSION['user_role'];
$is_admin = ($user_role == 'admin');

if (!$is_admin) {
    $commons->redirectTo(SITE_PATH . 'index.php');
}

?>

<div class="dashboard">
    <div class="col-md-12 col-sm-12" id="employee_table">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Reconnection Requests</h4>
            </div>
            <div class="panel-body">
                <?php if (isset($_SESSION['success'])) { ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success'] ?>
                    </div>
                <?php
                    unset($_SESSION['success']);
                } ?>
                <?php if (isset($_SESSION['errors'])) { ?>
                    <div class="alert alert-danger">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach ?>
                    </div>
                <?php
                    unset($_SESSION['errors']);
                } ?>
            </div>
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table table-striped table-bordered" id="grid-basic" style="min-width: 1200px; width: 100%;">
                    <thead class="thead-inverse">
                        <tr class="info">
                            <th>Client Name</th>
                            <th>Employer</th>
                            <th>Amount Paid</th>
                            <th>Payment Method</th>
                            <th>Reference Number</th>
                            <th>Payment Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script type="text/javascript">
    function viewData() {
        $.ajax({
            method: "GET",
            url: "reconnection_requests_approve.php",
            success: function(data) {
                $('tbody').html(data);
            }
        });
    }

    window.onload = function() {
        viewData();
    };
</script>