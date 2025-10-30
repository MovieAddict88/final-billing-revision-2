<?php
require_once "includes/headx.php";
require_once "includes/classes/admin-class.php";

$admins = new Admins($dbh);

$user_role = $_SESSION['user_role'] ?? 'admin';

if ($user_role == 'employer') {
    $employer_id = $_SESSION['user_id'];
    $data = $admins->fetchCustomerStatusByEmployer($employer_id);
} else {
    $location = $_SESSION['user_location'] ?? '';
    $data = $admins->fetchCustomerStatusByLocation($location);
}

header('Content-Type: application/json');
echo json_encode($data);
?>