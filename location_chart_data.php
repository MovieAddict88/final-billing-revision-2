<?php
require_once "includes/headx.php";
require_once "includes/classes/admin-class.php";

$admins = new Admins($dbh);
$data = $admins->fetchCustomerCountByLocation();

header('Content-Type: application/json');
echo json_encode($data);
?>