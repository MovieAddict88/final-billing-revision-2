<?php
session_start();
require_once 'config/dbconnection.php';
require_once 'includes/classes/admin-class.php';

$dbh = new Dbconnect();
$admins = new Admins($dbh);

if (!isset($_SESSION['admin_session']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $requestId = $_GET['id'];
    if ($admins->approveReconnectionRequest($requestId)) {
        $_SESSION['success'] = 'Reconnection request approved successfully.';
    } else {
        $_SESSION['errors'] = ['Failed to approve reconnection request.'];
    }
} else {
    $_SESSION['errors'] = ['Invalid request.'];
}

header('Location: reconnection_requests.php');
exit();
?>