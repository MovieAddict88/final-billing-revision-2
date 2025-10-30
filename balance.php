<?php
require_once "includes/header.php";

$balanceSummary = $commons->getBalanceSummary();
$total_rows = $balanceSummary['total_rows'];
$total_balance = $balanceSummary['total_balance'];
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Balance Summary</h1>
            <hr>
            <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Total Records</th>
                        <th>Total Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $total_rows ?></td>
                        <td>₱<?= number_format($total_balance, 2) ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>