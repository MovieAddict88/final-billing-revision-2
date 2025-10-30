<?php


	/* 
	 * We process the admin login form here
	 */

	// Start from getting the hader which contains some settings we need
	require_once 'includes/headx.php';


	// require the admins class which containes most functions applied to admins
	require_once 'config/dbconnection.php';
	$dbh = new Dbconnect();
	require_once "includes/classes/admin-class.php";

	$admins 	= new Admins($dbh);

	// Let's process the form now
	// Starting by checking if the forme has been submitted
	if (!isset($_POST) || sizeof($_POST) == 0 )
	{
		session::set('error', 'Submit the form first.');
		$commons->redirectTo(SITE_PATH.'login.php');
	}

	// If the form is submitted, let's check if the fields are not empty
	if ($commons->isFieldEmpty($_POST['username']) || $commons->isFieldEmpty($_POST['password']) ) 
	{
		session::set('error', 'All fields are required.');
		$commons->redirectTo(SITE_PATH.'login.php');

	}

	// Now let's check if the the username and password match a line in our table
	
	$user_name = htmlspecialchars( $_POST['username'], ENT_QUOTES, 'UTF-8' );
	$user_pwd = htmlspecialchars( $_POST['password'], ENT_QUOTES, 'UTF-8' );

	$login_data = $admins->loginAdmin($user_name, $user_pwd);

	if (!$login_data)
	{
		session::set('error', 'Cannot connect you. Check your credentials.');
		$commons->redirectTo(SITE_PATH.'login.php');

	}

	// Otherwise we can set a session to the admin and send him to the dashboard
	// The session will hold the username, role, and location.
	session::set('admin_session', $login_data->user_name);
	session::set('user_id', $login_data->user_id);
	session::set('user_role', $login_data->role);
	session::set('user_location', $login_data->location);
	$commons->redirectTo(SITE_PATH.'index.php');
