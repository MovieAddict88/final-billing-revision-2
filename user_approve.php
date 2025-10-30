<?php

	// Start from getting the hader which contains some settings we need
	require_once 'includes/headx.php';

	// require the admins class which containes most functions applied to admins
	require_once 'config/dbconnection.php';
	$dbh = new Dbconnect();
	require_once "includes/classes/admin-class.php";

	$admins	= new Admins($dbh);

	// check if the form is submitted
	$page = isset($_GET[ 'p' ])?$_GET[ 'p' ]:'';

	if($page == 'add'){
			$username = $_POST['username'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			$repassword = $_POST['repassword'];
			$fullname = $_POST['fullname'];
			$address = $_POST['address'];
			$contact = $_POST['contact'];
			$role = $_POST['role'];
			$profile_pic = null;

			if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
				$upload_dir = 'uploads/';
				if (!is_dir($upload_dir)) {
					mkdir($upload_dir, 0755, true);
				}

				$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
				$file_type = mime_content_type($_FILES['profile_pic']['tmp_name']);

				if (!in_array($file_type, $allowed_types)) {
					$response['status'] = 'error';
					$response['message'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
					header('Content-Type: application/json');
					echo json_encode($response);
					exit();
				}

				$filename = uniqid() . '-' . basename($_FILES['profile_pic']['name']);
				$profile_pic = $upload_dir . $filename;

				if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic)) {
					$profile_pic = null;
				}
			}

			$response = array();
			// Check if password are the same
			if (!$admins->ArePasswordSame($_POST['password'], $_POST['repassword']))
			{
				$response['status'] = 'error';
				$response['message'] = 'The two passwords do not match.';
			}elseif ($admins->adminExists($_POST['username'])) {
				$response['status'] = 'error';
				$response['message'] = 'This username is already in use by another admin.';
			}elseif (!$admins->addNewAdmin($username, $password, $email, $fullname, $address, $contact, $role, null, $profile_pic)) {
				$response['status'] = 'error';
				$response['message'] = 'An error occured while saving the new admin.';
			}else{
				$response['status'] = 'success';
				$response['message'] = 'New admin added successfully!';
				unset($_POST['repassword']);
			}
			header('Content-Type: application/json');
			echo json_encode($response);
			exit();
	}else if($page == 'del'){
		$id = $_POST['id'];
		if (!$admins->deleteUser($id)) 
		{
			echo "Sorry Data could not be deleted !";
		}else {
			echo "Well! You've successfully deleted a user!";
		}

	}else if($page == 'edit'){
		$username = $_POST['username'];
		$email = $_POST['email'];
		$full_name = $_POST['full_name'];
		$address = $_POST['address'];
		$contact = $_POST['contact'];
		$user_id = $_POST['user_id'];
		if (!$admins->updateAdmin($user_id, $username, $email, $full_name, $address, $contact)) 
		{	
			//echo "$user_id $username $email $full_name $address $contact";
			echo "Sorry Data could not be Updated !";
		}else {
			$commons->redirectTo(SITE_PATH.'user.php');
		}

    }else{
        // Pagination and search
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $offset = ($page - 1) * $limit;
        $users = $admins->fetchAdminPage($offset, $limit, $q); 
        $total = $admins->countAdmin($q);
        $totalPages = ($limit > 0) ? (int)ceil($total / $limit) : 1;
        if (isset($users) && sizeof($users) > 0) {
            foreach ($users as $user){ ?>
				<tr>
					<td scope="row"><?=$user->user_id ?></td>
					<td>
						<button type="button" id="edit" class="btn btn-success btn-sm btn-action" data-toggle="modal" data-target="#edit-<?=$user->user_id?>">EDIT</button>
						<div class="fade modal" id="edit-<?=$user->user_id?>">
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
											<input type="hidden" id="<?=$user->user_id ?>" value="<?=$user->user_id?>">

											<div class="form-group has-success">
												<label for="name">Full Name</label>
												<input type="text" class="form-control" id="fnm-<?=$user->user_id?>"  value="<?=$user->full_name?>" required>
											</div>
											<div class="form-group">
												<label for="Username">Username</label>
												<input type="text" class="form-control" id="usr-<?=$user->user_id?>"  value="<?=$user->user_name?>" required>
											</div>
											<div class="form-group">
												<label for="email">Email</label>
												<input type="text" class="form-control" id="em-<?=$user->user_id?>"  value="<?=$user->email?>" required>
											</div>
											<div class="form-group">
												<label for="details">Address</label>
												<input type="text" class="form-control" id="ad-<?=$user->user_id?>"  value="<?=$user->address?>" required>
											</div>
											<div class="form-group">
												<label for="contact">Contact</label>
												<input type="text" class="form-control" id="con-<?=$user->user_id?>"   value="<?=$user->contact?>" required>
											</div>
										</div>
										<div class="modal-footer">
											<button type="submit"  onclick="updateData(<?=$user->user_id?>)" class="btn btn-primary">Update</button>
											<a href="#" class="btn btn-warning" data-dismiss="modal">Cancel</a>
										</div>
									</form>
								</div>
							</div>
						</div>
						<button type="submit" id="delete" onclick="delData(<?=$user->user_id ?>)" class="btn btn-warning btn-sm btn-action">DELETE</button>
					</td>
					<td class="search"><?=$user->user_name?></td>
					<td class="search"><?=$user->full_name?></td>
					<td class="search"><?=$user->email?></td>
					<td class="search"><?=$user->contact?></td>
					<td class="search"><?=$user->address?></td>
				</tr>
            <?php
            }
            $prevDisabled = ($page <= 1) ? 'disabled' : '';
            $nextDisabled = ($page >= $totalPages) ? 'disabled' : '';
            ?>
            <tr class="pagination-row" data-page="<?=$page?>" data-total="<?=$total?>" data-limit="<?=$limit?>" data-query="<?=htmlspecialchars($q)?>">
                <td colspan="7" class="text-center">
                    <button class="btn btn-default btn-sm page-prev" <?=$prevDisabled?>>Prev</button>
                    <span class="mx-2">Page <?=$page?> of <?=$totalPages?></span>
                    <button class="btn btn-default btn-sm page-next" <?=$nextDisabled?>>Next</button>
                </td>
            </tr>
            <?php
        }
    }
?>