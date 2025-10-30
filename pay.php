<?php
	require_once "includes/headx.php";
	if (!isset($_SESSION['admin_session']) )
	{
		$commons->redirectTo(SITE_PATH.'login.php');
	}
	require_once "includes/classes/admin-class.php";
    $admins	= new Admins($dbh);
    $id = isset($_GET[ 'customer' ])?$_GET[ 'customer' ]:'';
    $action = isset($_GET['action']) ? $_GET['action'] : 'pay';
    ?>
    <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      color: #000;
    }
    .header {
      display: flex;
      align-items: center;
      border-bottom: 2px solid #ccc;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    .logo {
      width: 80px;
      margin-right: 20px;
    }
    .company-details {
      font-size: 14px;
    }
    h2 {
      text-align: center;
      text-decoration: underline;
      margin: 20px 0;
    }
    .info, .account {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .info td, .account td {
      padding: 6px 10px;
    }
    .account {
      border: 1px solid #000;
    }
    .account td {
      border: 1px solid #000;
      text-align: left;
    }
    .amount-due {
      text-align: right;
      font-size: 18px;
      font-weight: bold;
      margin-top: 15px;
    }
    .footer {
      margin-top: 40px;
      font-size: 13px;
    }
    .highlight {
      background: #f8f8a6;
      font-weight: bold;
    }
    @media print {
      .no-print {
        display: none;
      }
    }
  </style>
<!doctype html>
<html lang="en" class="no-js">
<head>
	<meta charset=" utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="component/css/bootstrap.css"> <!-- CSS bootstrap -->
	<link rel="stylesheet" href="component/css/bootstrap-select.min.css"> <!-- CSS bootstrap -->
	<link rel="stylesheet" href="component/css/style.css"> <!-- Resource style -->
    <link rel="stylesheet" href="component/css/reset.css"> <!-- Resource style -->
	<link rel="stylesheet" href="component/css/invoice.css"> <!-- CSS bootstrap -->
	<script src="component/js/modernizr.js"></script> <!-- Modernizr -->
	<title>Invoice | Cornerstone</title>
</head>
<body>
<div class="container">
        <?php
            $info = $admins->getCustomerInfo($id);
            if (isset($info) && is_object($info)) {
            $package_id = $info->package_id;
            $packageInfo = $admins->getPackageInfo($package_id);
        ?>
    <div class="row">
        <div class="brand"><img src="component/img/cs.png" alt=""></div>
        <?php if ($action == 'bill'): ?>
            <h2>INVOICE</h2>
        <?php else: ?>
            <h2>STATEMENT OF ACCOUNT</h2>
        <?php endif; ?>
        </div>
        <div class="row no-print">
            <div class="col-xs-12">
                <button class="btn btn-primary pull-right" onclick="window.print();">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>
        <div class="pull-right">Date: <?=date("j F Y")?></div><br>
        <?php
            $has_unpaid = false;
            $bills = $admins->fetchAllIndividualBill($id);
            if (isset($bills) && sizeof($bills) > 0) {
                foreach ($bills as $bill) {
                    if ($bill->status == 'Unpaid') {
                        $has_unpaid = true;
                        break;
                    }
                }
            }
        ?>
        <?php if ($action != 'bill' && $has_unpaid): ?>
            <h3>Subject   : NOTICE FOR DISCONNECTION</h3>
        <?php endif; ?>
        <div class="em"><b>Name   : </b> <em><?=$info->full_name?></em></div>
        <div class="em"><b>Address:</b> <em><?=$info->address ?></em></div>
        <div class="em"><b>Contact :</b> <em><?=$info->contact ?></em> </div>
        <div class="highlight" style="padding:8px 10px; margin:10px 0;">Account No.: <b><?=$info->ip_address?></b></div>
        <?php } ?>
    <div class="row">
        <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-inverse">
                <tr>
                    <th colspan="2">Account Details</th>
                </tr>
            </thead>
            <tbody>
            <?php
                // Build rows in the requested format and compute totals from real data
                $total_due = 0.00;
                $bill_ids = [];
                $monthArray = [];
                $packageName = isset($packageInfo->name) ? $packageInfo->name : 'N/A';

                echo '<tr><td><b>Plan</b></td><td>'.htmlspecialchars($packageName).'</td></tr>';

                if (isset($bills) && sizeof($bills) > 0){
                    foreach ($bills as $bill){
                        $year = date('Y', strtotime($bill->g_date));
                        $monthNum = date('n', strtotime($bill->g_date)); // aligns with generation month
                        // Derive billing period boundaries using a standard billing day-of-month (5th)
                        $billingDay = 5;
                        $startTs = mktime(0, 0, 0, $monthNum - 1, $billingDay, $year);
                        $endTs   = mktime(0, 0, 0, $monthNum, $billingDay, $year);
                        $period  = date('F j, Y', $startTs) . ' - ' . date('F j, Y', $endTs);

                        // Determine the amount still due on this bill
                        $due_amount = (is_numeric($bill->balance) && (float)$bill->balance > 0)
                            ? (float)$bill->balance
                            : (float)$bill->amount;

                        // Only show rows that still have amount due (Unpaid or with positive balance)
                        if ($bill->status !== 'Paid' && $due_amount > 0) {
                            $total_due += $due_amount;
                            $monthArray[] = $bill->r_month;
                            $bill_ids[] = $bill->id;

                            echo '<tr>';
                            echo '<td><b>Billing Period</b><br><span style="font-weight:normal;">('.htmlspecialchars($bill->r_month).')</span></td>';
                            echo '<td>'.htmlspecialchars($period).'<br><b>Amount:</b> ₱'.number_format($due_amount, 2).'</td>';
                            echo '</tr>';
                        }
                    }
                } else {
                    echo '<tr><td colspan="2" class="text-center">No bills found.</td></tr>';
                }
            ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php if ($action != 'bill' && (isset($total_due) && $total_due > 0)): ?>
    <p class="amount-due">
        <span>TOTAL AMOUNT DUE:</span>
        <span>  ₱<?=number_format($total_due, 2)?></span>
    </p>

    <div class="row no-print">
     <form class="form-inline" action="post_approve.php" method="POST">
            <input type="hidden" name="customer" value="<?=(isset($info->id) ? $info->id : '')?>">
            <input type="hidden" name="bills" value="<?=implode($bill_ids,',')?>">
            <div class="form-group">
            <label for="months"></label>
            <select class="selectpicker" name="months[]" id="months" multiple required title="Select months">
                  <?php
                       if (!empty($monthArray)) {
                          foreach ($monthArray as $month) {
                            echo '<option value="'.$month.'" selected>'.$month.'</option>';
                          }
                       }
                    ?>
            </select>
            </div>
            <div class="form-group">
            <label class="sr-only" for="discount">Discount</label>
            <input type="number" class="form-control" name="discount" id="discount" placeholder="Discount" >
            </div>
            <div class="form-group">
            <label class="sr-only" for="total">Payment</label>
            <input type="number" class="form-control disabled" name="total" id="total" placeholder="total" required="" value="<?=$total_due?>">
            </div>
            <button type="submit" class="btn btn-primary">Paid</button>
        </form>
    </div>
    <?php endif; ?>
    <hr>
    <?php
        // Invoice Payment Ledger section
        $paymentLedger = $admins->fetchPaymentHistoryByCustomer($id);
    ?>
    <h3>Invoice Payment Ledger</h3>
    <div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="thead-inverse">
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
            <?php if ($paymentLedger && count($paymentLedger) > 0): ?>
                <?php foreach ($paymentLedger as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('F j, Y, g:i a', strtotime($row->paid_at))) ?></td>
                        <td><?= htmlspecialchars($row->r_month) ?></td>
                        <td><?= htmlspecialchars($row->package_name ?: 'N/A') ?></td>
                        <td><?= number_format((float)$row->amount, 2) ?></td>
                        <td><?= number_format((float)$row->paid_amount, 2) ?></td>
                        <td><?= number_format((float)$row->balance_after, 2) ?></td>
                        <td><?= htmlspecialchars($row->payment_method ?: 'N/A') ?></td>
                        <td><?= htmlspecialchars($row->reference_number ?: 'N/A') ?></td>
                        <td><?= htmlspecialchars($row->employer_name ?: 'Admin') ?></td>
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
    <div class="sign pull-right">Authorized Signature</div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="component/js/bootstrap-select.min.js"></script>
<script>
    $('#months').on('changed.bs.select', function (e) {
        console.log(this.value);
      });
</script>