<?php
session_start();
require_once 'config/dbconnection.php';
require_once 'includes/classes/admin-class.php';

$dbh = new Dbconnect();
$admins = new Admins($dbh);

if (isset($_POST['customer_id'])) {
    $customerId = $_POST['customer_id'];

    if ($admins->reconnectCustomer($customerId)) {
        $_SESSION['success'] = 'Customer reconnected successfully.';
        echo json_encode(['status' => 'success']);
    } else {
        $_SESSION['errors'] = ['Failed to reconnect customer.'];
        echo json_encode(['status' => 'error']);
    }
}
?>