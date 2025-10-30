<?php
	// Start from getting the hader which contains some settings we need
	require_once 'includes/header.php';
	require_once 'config/dbconnection.php';
	$dbh = new Dbconnect();
	require_once "includes/classes/admin-class.php";
	$admins	= new Admins($dbh);
	// Redirect visitor to the login page if he is trying to access
	// this page without being logged in
	if (!isset($_SESSION['admin_session']) )
	{
		$commons->redirectTo(SITE_PATH.'login.php');
	}
?>
			<!-- <button disabled="disabled" class="btn btn-success">Generate Now</button> -->
	<div class="dashboard">
		<div class="col-md-12 col-sm-12">
		<div class="col-md-6">
			<!-- <h4>Customer Billing: Month of October</h4> -->
			<a href='paid_bills.php' class="btn btn-primary">Paid Bills</a> 
		</div>
		<div class="col-md-6">
			<form class="form-inline pull-right">
			  <div class="form-group">
			    <label class="sr-only" for="search">Search for</label>
			    <div class="input-group">
			      <div class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div>
			      <input type="text" class="form-control" id="search" placeholder="Type a name">
			      <div class="input-group-addon"></div>
			    </div>
			  </div>
			</form>
			<a href='bill_generation.php' class="btn btn-success pull-right">Generate Now</a> 
			
		</div>
		<br><br>
		
		</div>
		<div class="col-md-12 col-sm-12" id="bill_table">
			<?php
			  $billing = $admins->fetchBilling();
			  if (isset($billing) && sizeof($billing) > 0){ ?>
				<div class="table-responsive">
				<table class="table table-striped table-bordered">
				<thead class="thead-inverse">
				  <tr class="info">
				    <th>ID </th>
				    <th>Name</th>
				    <th>Generated on</th>
				    <th>Package</th>
				    <th>Months</th>
				    <th>Amounts</th>
				    <th>Due Date</th>
				    <th>Status</th>
				    <th>Action</th>
				  </tr>
				</thead>
			  <tbody>
				<?php
			  	foreach ($billing as $bill) {
			  		$client_id = $bill->customer_id;
			  		$customer_info = $admins->getCustomerInfo($client_id);
			  		$customer_name = $customer_info->full_name;
						$package_id = !empty($bill->package_id) ? $bill->package_id : $customer_info->package_id;
						$packageInfo = $admins->getPackageInfo($package_id);
						$package_name = $packageInfo ? $packageInfo->name : 'N/A';
			  	 	?>
			  <tr>
			  	<td scope="row"><?=$bill->id?></td>
			  	<td><?=$customer_name?></td>
			  	<td><?=$bill->g_date?></td>
			  	<td><?=$package_name?></td>
			  	<td><?=$bill->months?></td>
			  	<td>₱<?=number_format($bill->total, 2)?></td>
				<td><?=$customer_info->due_date?></td>
				<td>
					<?php 
						$status = $bill->status;
						$status_class = '';
						switch($status) {
							case 'Paid':
								$status_class = 'label-success';
								break;
							case 'Balance':
								$status_class = 'label-warning';
								break;
							case 'Unpaid':
								$status_class = 'label-danger';
								break;
							case 'Pending':
								$status_class = 'label-info';
								break;
							default:
								$status_class = 'label-default';
						}
					?>
					<span class="label <?=$status_class?>"><?=$status?></span>
				</td>
				<td>
					<?php if ($bill->status == 'Pending'): ?>
						<button type="button" onClick="view_payment(<?=$bill->id?>)" class="btn btn-warning">View</button>
					<?php else: ?>
						<button type="button" onClick="pay(<?=$client_id?>)" class="btn btn-info">Pay</button>
						<button onClick="bill(<?=$client_id?>)" type="button" class="btn btn-info">Bill</button>
					<?php endif; ?>
				</td>
			  </tr>
			  <?php
			  	}
				}else{
					?>
						<h1>Congratulations ! No due is left to be paid !</h1>
					<?php
				}
			?>
			  </tbody>
				</table>
				</div>
			<div>
			</div>
			
		</div>
	</div>

	<?php include 'includes/footer.php'; ?>
	<script type="text/javascript">
		function pay(id) {
		let left = (screen.width/2)-(600/2);
  	let top = (screen.height/2)-(800/2);
		let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=600,height=800,left=${left},top=${top}`;
		open('pay.php?customer='+id, 'Hello World !', params)
		}
		function bill(id) {
		let left = (screen.width/2)-(600/2);
  	let top = (screen.height/2)-(800/2);
		let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=600,height=800,left=${left},top=${top}`;
		open('pay.php?customer='+id+'&action=bill', 'Invoice', params)
		}
		function view_payment(id) {
		let left = (screen.width/2)-(600/2);
		let top = (screen.height/2)-(800/2);
		let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=600,height=800,left=${left},top=${top}`;
		open('view_payment.php?id='+id, 'Payment Details', params)
		}
	</script>
