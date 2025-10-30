<?php
	require_once "includes/headx.php";
	if (!isset($_SESSION['admin_session']) )
	{
		$commons->redirectTo(SITE_PATH.'login.php');
	}
	require_once "includes/classes/admin-class.php";
    $admins	= new Admins($dbh);
    $id = isset($_GET[ 'customer' ])?$_GET[ 'customer' ]:''; 
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
  </style>
  <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header { text-align: center; }
        .header h2 { margin: 0; }
        .details { margin: 20px 0; }
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: center; }
        .amount-due { font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 40px; font-size: 12px; }
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
	<title>Invoice | Netway</title>
</head>
<body>
<div class="container">
        <?php
            $info = $admins->getCustomerInfo($id); 
            if (isset($info) && sizeof($info) > 0) {
            $package_id = $info->package_id;
            $packageInfo = $admins->getPackageInfo($package_id);
        ?>
    <div class="row">
       <div class="brand"><img src="component/img/cs.png" alt=""></div>
    <h2>CORNERSTONE INNOVATE TECH SOL</h2>
    <p>#11 Caco Apartment Mambog IV, Bacoor Cavite<br>
       Brix Bryan S. Villas-Prog.<br>
       NON-VAT Reg. TIN: 434-028-840-000</p>
</div>

        <h2><strong>STATEMENT OF ACCOUNT</h2><div
        </div></div>
        <div class="pull-right">Date: <?=date("j F Y")?></div><br></div>
        <h3><strong>Subject   : NOTICE FOR DISCONNECTION</h3><div
        <div class="em"><b>Name   : </b> <em><?=$info->full_name?></em></div>
        <div class="em"><b>Address:</b> <em><?=$info->address ?></em></div>
        <div class="em"><b>Contact :</b> <em><?=$info->contact ?></em> </div>
        <div class="em"><b>Account Number:</b> <em><?=$info->ip_address?></em></div>
        <div class="em"><b>Due Date:</b> <em><?=$info->due_date?></em></div>
        <?php } ?>
        <div class="row">
        <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-inverse">
                <tr>
                    <th>Plan </th>
                    <th>25Mbps</th>
                    <th>Amount</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $bills = $admins->fetchPaymentSlip($id);
                    $amount = $_POST['amount'];
                    if (isset($bills) && sizeof($bills) > 0){
                        
                ?>
                <tr>
                     <td><?=date("F j", strtotime("-1 month"))?></td>  <!-- last month -->
                <td><?=date("F j")?></td>                         <!-- present month -->
                       <td><?=$bills->bill_amount?></td>
                        <td>₱<?=number_format($balance, 2)?></td>
                    </tr>
                    </tr>
            </tbody>
           <tfoot>
                 
                    <tr>
                        <th>In Words</td>
                        <th colspan="3"><?=getToText($bills->bill_amount)?> Pesos.</th>
                    </tr>
                </tfoot>
            <?php 
                } ?>
        </table>
        </div>
    </div>
    <hr>
    <?php
        // Invoice Payment Ledger section for receipt view
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
                        <td><?= htmlspecialchars(date('Y-m-d h:i:s A', strtotime($row->paid_at))) ?></td>
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
         <p class="amount-due">TOTAL AMOUNT DUE: <td><?=$bills->bill_amount?></td>

   <h2><strong>We appreciate your prompt payment and value as a customer.</h2><br></br>
    <p><strong>Contact us</strong><br>
    FB Page | Customer Service: 0951-6651142 | Billing Department: 0985-3429675</p>
    <p><strong>CORNERSTONE INNOVATE TECH SOL</strong></p>
  </div>
   <div class="printbutton hide-on-small-only pull-left"><a href="#" onClick="javascript:window.print()">Print</a></div>
    <div class="footer">
</div>
</body>
<?php include 'includes/footer.php'; ?>
<script src="component/js/bootstrap-select.min.js"></script>
<script>
    $('#months').on('changed.bs.select', function (e) {
        console.log(this.value);
      });
</script>