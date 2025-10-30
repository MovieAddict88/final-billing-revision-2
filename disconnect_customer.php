<?php
require_once 'includes/headx.php';
require_once 'config/dbconnection.php';
$dbh = new Dbconnect();
require_once "includes/classes/admin-class.php";
$admins = new Admins($dbh);

if (isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];
    $disconnected_by = $_SESSION['admin_session']->user_id ?? null;
    
    if ($admins->disconnectCustomer($customer_id, $disconnected_by)) {
        $_SESSION['success'] = 'Customer successfully disconnected and moved to disconnected clients.';
    } else {
        $_SESSION['errors'] = ['Failed to disconnect customer.'];
    }
} else {
    $_SESSION['errors'] = ['No customer ID provided.'];
}

// Redirect back to appropriate page
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: customers.php");
}
exit();
?>