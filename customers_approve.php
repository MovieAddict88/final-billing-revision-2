<?php

	// Start from getting the hader which contains some settings we need
	require_once 'includes/headx.php';

	// require the admins class which containes most functions applied to admins
	require_once "includes/classes/admin-class.php";

	$admins	= new Admins($dbh);

	// check if the form is submitted
	$page = isset($_GET[ 'p' ])?$_GET[ 'p' ]:'';

	if($page == 'add'){
			$full_name = $_POST['full_name'];
			$nid = $_POST['nid'];
			$address = $_POST['address'];
			$package = $_POST['package'];
			$conn_location = $_POST['conn_location'];
			$email = $_POST['email'];
			$ip_address = $_POST['ip_address'];
			$conn_type = $_POST['conn_type'];
			$contact = $_POST['contact'];
			$employer_id = $_POST['employer'];
			$due_date = $_POST['due_date'];
			$login_code = bin2hex(random_bytes(16));

			if (isset($_POST)) 
			{

				$errors = array();
				// Check if password are the same
				$customer_id = $admins->addCustomer($full_name, $nid, $address, $conn_location, $email, $package, $ip_address, $conn_type, $contact, $login_code, $employer_id, $due_date);
				if ($customer_id)
				{
					$packageInfo = $admins->getPackageInfo($package);
					$amount = $packageInfo->fee;
					$r_month = date('F');
					$admins->billGenerate($customer_id, $r_month, $amount);
					session::set('confirm', 'New customer added successfully!');
				}else{
					session::set('errors', ['Couldn\'t Add New Customer']);
				}
			}
	}else if($page == 'del'){
		$id = $_POST['id'];
		if (!$admins->deletecustomer($id)) 
		{
			echo "Sorry Data could not be deleted !";
		}else {
			echo "Well! You've successfully deleted a product!";
		}

	}else if($page == 'edit'){
		$id = $_POST['id'];
		$full_name = $_POST['full_name'];
		$nid = $_POST['nid'];
		$address = $_POST['address'];
		$conn_location = $_POST['conn_location'];
		$email = $_POST['email'];
		$package = $_POST['package'];		
		$ip_address = $_POST['ip_address'];
		$conn_type = $_POST['conn_type'];
		$contact = $_POST['contact'];
		$employer_id = $_POST['employer'];
		$due_date = $_POST['due_date'];
		if (!$admins->updateCustomer($id, $full_name, $nid, $address, $conn_location, $email, $package, $ip_address,  $conn_type, $contact, $employer_id, $due_date))
		{	
			//echo "$id $customername $email $full_name $address $contact";
			echo "Sorry Data could not be Updated !";
		}else {
			$commons->redirectTo(SITE_PATH.'customers.php');
		}

    }else{
        // Pagination and search params
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';

        $offset = ($page - 1) * $limit;

        $customers = $admins->fetchCustomersPage($offset, $limit, $q);
        $total = $admins->countCustomers($q);
        $totalPages = ($limit > 0) ? (int)ceil($total / $limit) : 1;
        $employers = $admins->getEmployers();
        if (isset($customers) && sizeof($customers) > 0) {
            foreach ($customers as $customer){
				$packageInfo = $admins->getPackageInfo($customer->package_id);
				$package_name = $packageInfo->name;
				 ?>
				<tr>
					<td scope="row"><?=$customer->id ?></td>
					<td>
						<button type="button" id="edit" class="btn btn-success btn-sm btn-action" data-toggle="modal" data-target="#edit-<?=$customer->id?>">EDIT</button>
						<div class="fade modal" id="edit-<?=$customer->id?>">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">×</button>
										<h4>Edit Details</h4>
									</div>
									<form method="POST">
										<div class="modal-body">
											<!-- The async form to send and replace the modals content with its response -->
											<!-- form content -->
											<input type="hidden" id="<?=$customer->id ?>" value="<?=$customer->id?>">

											<div class="form-group has-success">
												<label for="name">Full Name</label>
												<input type="text" class="form-control" id="fnm-<?=$customer->id?>"  value="<?=$customer->full_name?>" required>
											</div>
											<div class="form-group">
												<label for="nid">NID</label>
												<input type="text" class="form-control" id="nid-<?=$customer->id?>"  value="<?=$customer->nid?>" required>
											</div>
											<div class="form-group">
												<label for="address">Address</label>
												<input type="text" class="form-control" id="ad-<?=$customer->id?>"  value="<?=$customer->address?>" required>
											</div>
											<div class="form-group">
											<label for="package">Select Package</label>
												<select class="form-control form-control-sm" name="package" id="pk-<?=$customer->id?>">
												<option value='<?=$customer->package_id?>'><?=$package_name?></option>
												<?php 
													$packages = $admins->getPackages();
													if (isset($packages) && sizeof($packages) > 0){ 
														foreach ($packages as $package) { ?>
														<option value='<?=$package->id?>'><?=$package->name?></option>
												<?php }} ?>
												</select>
											</div>
											<div class="form-group">
												<label for="ip">IP Address</label>
												<input type="text" class="form-control" id="ip-<?=$customer->id?>"  value="<?=$customer->ip_address?>" required>
											</div>
											<div class="form-group">
												<label for="contact">Contact</label>
												<input type="text" class="form-control" id="con-<?=$customer->id?>"   value="<?=$customer->contact?>" required>
											</div>
											<div class="form-group">
												<label for="due_date">Due Date</label>
												<input type="date" class="form-control" id="due_date-<?=$customer->id?>"   value="<?=$customer->due_date?>" required>
											</div>
											<div class="form-group">
												<label for="conlocation">Connection Location</label>
												<input type="text" class="form-control" id="conn_loc-<?=$customer->id?>"   value="<?=$customer->conn_location?>" required>
											</div>
											<div class="form-group">
												<label for="type">Connection Type</label>
												<input type="text" class="form-control" id="ct-<?=$customer->id?>"   value="<?=$customer->conn_type?>" required>
											</div>
											<div class="form-group">
												<label for="email">Email</label>
												<input type="text" class="form-control" id="em-<?=$customer->id?>"   value="<?=$customer->email?>" required>
											</div>
											<div class="form-group">
												<label for="package">Select Employer</label>
												<select class="form-control form-control-sm" name="employer" id="emp-<?=$customer->id?>">
													<option value=''>Select an employer</option>
													<?php
													if (isset($employers) && sizeof($employers) > 0){
														foreach ($employers as $employer) { ?>
															<option value='<?=$employer->user_id?>' <?=$customer->employer_id == $employer->user_id ? 'selected' : ''?>><?=$employer->full_name?></option>
														<?php }} ?>
												</select>
											</div>
										</div>
										<div class="modal-footer">
											<button type="submit"  onclick="updateData(<?=$customer->id?>)" class="btn btn-primary">Update</button>
											<a href="#" class="btn btn-warning" data-dismiss="modal">Cancel</a>
										</div>
									</form>
								</div>
							</div>
						</div>
						<a href="customer_details.php?id=<?=$customer->id?>" class="btn btn-info btn-sm btn-action">VIEW</a>
						<button type="submit" id="delete" onclick="delData(<?=$customer->id ?>)" class="btn btn-warning btn-sm btn-action">DELETE</button>
						<button type="button" class="btn btn-primary btn-sm btn-action" onclick="openRemarkModal(<?=$customer->id?>, '<?=htmlspecialchars($customer->remarks)?>')">REMARK</button>
						<?php
						$isOverdue = false;
						$dueDate = $customer->due_date;
						$status = $admins->getCustomerStatus($customer->id);
						if ($status != 'Paid' && $dueDate) {
							$today = new DateTime();
							$dueDateObj = new DateTime($dueDate);
							$today->setTime(0, 0, 0);
							$dueDateObj->setTime(0, 0, 0);

							if ($dueDateObj < $today) {
								$isOverdue = true;
							}
						}
						if ($isOverdue): ?>
						<a href="disconnect_customer.php?customer_id=<?=$customer->id?>" class="btn btn-danger btn-sm btn-action">DISCONNECT</a>
						<?php endif; ?>
					</td>
					<td class="search"><?=$customer->full_name?></td>
					<td class="search"><?=$customer->employer_name ? $customer->employer_name : 'N/A'?></td>
					<td class="search"><?=$customer->nid?></td>
					<td class="search"><?=$customer->address?></td>
					<td class="search"><?=$package_name?></td>
					<td class="search"><?=$customer->ip_address?></td>
					<td class="search"><?=$customer->email?></td>
					<td class="search"><?=$customer->contact?></td>
					<td class="search"><?=$customer->conn_type?></td>
                    <td class="search">
                        <?php 
                            $status = $admins->getCustomerStatus($customer->id);
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
                                default:
                                    $status_class = 'label-default';
                            }
                        ?>
                        <span class="label <?=$status_class?>"><?=$status?></span>
                    </td>
                    <td class="search"><?=number_format($customer->total_paid, 2)?></td>
                    <td class="search"><?=number_format($customer->total_balance, 2)?></td>
					<td class="search"><?=$customer->login_code?></td>
					<td class="search"
						<?php
						$dueDate = $customer->due_date;
						$status = $admins->getCustomerStatus($customer->id);
						$style = '';
						if ($status == 'Paid') {
							$style = 'style="background-color: #D4EFDF;"'; // Light Green
						} else if ($dueDate) {
							$today = new DateTime();
							$dueDateObj = new DateTime($dueDate);
							$today->setTime(0,0,0);
							$dueDateObj->setTime(0,0,0);

							if ($dueDateObj < $today) {
								$style = 'style="background-color: #8B0000; color: white;"'; // Dark Red for overdue
							} else {
								$interval = $today->diff($dueDateObj);
								$days = $interval->days;

								if ($days <= 3) {
									$style = 'style="background-color: #FADBD8;"'; // Light Red
								} elseif ($days <= 7) {
									$style = 'style="background-color: #FDEBD0;"'; // Light Orange
								} else {
									$style = 'style="background-color: #D6EAF8;"'; // Light Blue
								}
							}
						}
						echo $style;
						?>
					><?=$customer->due_date?></td>
					<td class="search"><?=htmlspecialchars($customer->remarks)?></td>
				</tr>
            <?php
            }
            // Pagination controls row
            $prevDisabled = ($page <= 1) ? 'disabled' : '';
            $nextDisabled = ($page >= $totalPages) ? 'disabled' : '';
            $colspan = 15; // number of table columns
            ?>
            <tr class="pagination-row" data-page="<?=$page?>" data-total="<?=$total?>" data-limit="<?=$limit?>" data-query="<?=htmlspecialchars($q)?>">
                <td colspan="<?=$colspan?>" class="text-center">
                    <button class="btn btn-default btn-sm page-prev" <?=$prevDisabled?>>Prev</button>
                    <span class="mx-2">Page <?=$page?> of <?=$totalPages?></span>
                    <button class="btn btn-default btn-sm page-next" <?=$nextDisabled?>>Next</button>
                </td>
            </tr>
            <?php
        }
    }
?>