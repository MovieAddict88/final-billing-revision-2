<?php
	// Get the application settings and parameters
	require_once "includes/headx.php";
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
	<meta charset=" utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="component/css/bootstrap.css"> <!-- CSS bootstrap -->
    <link rel="stylesheet" href="component/css/style.css"> <!-- Resource style -->
    <link rel="stylesheet" href="assets/css/custom.css"> <!-- Shared responsive tweaks -->
	<script src="component/js/modernizr.js"></script> <!-- Modernizr -->
	<title>Cornerstone | Customer Portal</title>
</head>
<body>
<header class="cd-main-header">
    <a href="customer_dashboard.php" class="cd-logo"><img src="component/img/cd-logo.svg" alt="Logo"></a>
    <nav class="cd-main-nav">
        <ul>
            <li><a href="customer_dashboard.php">Dashboard</a></li>
            <li><a href="statement_of_account.php">Statement of Account</a></li>
            <li><a href="customer_logout.php">Logout</a></li>
        </ul>
    </nav>
</header>