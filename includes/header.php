<?php
	
	// Get the application settings and parameters

	require_once "includes/headx.php";
	if (!isset($_SESSION['admin_session']) )
	{
		$commons->redirectTo(SITE_PATH.'login.php');
	}
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
	<meta charset=" utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>

<!-- 	<link rel="stylesheet" href="component/css/reset.css">  -->
	<link rel="stylesheet" href="component/css/bootstrap.css"> <!-- CSS bootstrap -->
	<link rel="stylesheet" href="component/css/jquery.bootgrid.css"> <!-- Bootgrid stylesheet -->
	<link rel="stylesheet" href="component/css/style.css"> <!-- Resource style -->
	<link rel="stylesheet" href="component/css/reset.css"> <!-- Resource style -->
	<link rel="stylesheet" href="assets/css/custom.css"> <!-- Custom CSS -->
	<script src="component/js/modernizr.js"></script> <!-- Modernizr -->

  	
	<title>Cornerstone | ISP Solution</title>
</head>
<body>
	<header class="cd-main-header">
		<a href="#0" class="cd-logo text-white">Cornerstone | <?php echo ucfirst($_SESSION['user_role'] ?? 'Admin'); ?></a>
		
        <!-- Global search suggestions container -->
        <datalist id="global-suggestions"></datalist>

		<a href="#0" class="cd-nav-trigger"><span></span></a>

		<nav class="cd-nav">
			<ul class="cd-top-nav">
				<li class="has-children account">
					<a href="#0">
						<img src="component/img/cs.png" alt="avatar">
						<?php echo $_SESSION["admin_session"]; ?> (<?php echo ucfirst($_SESSION['user_role'] ?? 'Admin'); ?>)
					</a>
					</ul>
				</li>
			</ul>
		</nav>
	</header> <!-- .cd-main-header -->

	<main class="cd-main-content">
		<?php if ($_SESSION['user_role'] == 'admin'): ?>
		<nav class="cd-side-nav">
			<ul>
				<li class="overview">
					<a href="index.php">Dashboard</a>
				</li>
				<li class="has-children overview active">
					<a href="#0">Collection / Balance<!-- <span class="count">3</span> --></a>
					
					<ul>
						<li><a href="daily_data.php">Collect / Balance</a></li>						
						<li><a href="bills.php">Monthly Billing</a></li>
						<li><a href="balance.php">Balance Summary</a></li>
					</ul>
				</li>

				<li class="has-children overview active">
					<a href="#0">Products</a>
					<ul>
						<li><a href="production.php">Stock Entry</a></li>
						<li><a href="production_stat.php">Products Stock</a></li>						
					</ul>
				</li>
			</ul>

			<ul>
				<li class="cd-label">Administration</li>
				<li class="bookmarks">
					<a href="products.php">Products</a>
				</li>
				<li class="users">
					<a href="customers.php">Customers</a>
				</li>
				<li class="users">
					<a href="disconnected_clients.php">Disconnected Clients</a>
				</li>

				<li class="users">
					<a href="user.php">Users</a>
				</li>
				<li class="monitoring">
					<a href="employee_monitoring.php">Employee/Monitoring</a>
				</li>
				<li><a href="logout.php">Logout</a></li>
			</ul>
			<!-- <ul>
				<li class="cd-label">Action</li>
				<li class="action-btn"><a href="#0">INSERT DATA</a></li>
			</ul> -->
		</nav>
		<?php else: ?>
		<nav class="cd-side-nav">
			<ul>
				<li class="overview">
					<a href="index.php">Dashboard</a>
				</li>
				<li class="users">
					<a href="disconnected_clients.php">Disconnected Clients</a>
				</li>
				<li><a href="logout.php">Logout</a></li>
			</ul>
		</nav>
		<?php endif; ?>

		<div class="content-wrapper">
		<div class="container-fluid">
		