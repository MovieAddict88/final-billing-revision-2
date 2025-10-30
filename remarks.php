<?php
require_once "config/dbconnection.php";
require_once "includes/classes/admin-class.php";

$dbh = new Dbconnect();
$admins = new Admins($dbh);
if (isset($_POST['add_remark'])) {
    $customer_id = $_POST['customer_id'];
    $remark = $_POST['remark'];

    if ($admins->addRemark($customer_id, $remark)) {
        header("Location: index.php?success=Remark added successfully");
    } else {
        header("Location: index.php?error=Failed to add remark");
    }
}
if (isset($_POST['update_remark'])) {
    $customer_id = $_POST['customer_id'];
    $remark = $_POST['remark'];

    if ($admins->updateRemark($customer_id, $remark)) {
        header("Location: index.php?success=Remark updated successfully");
    } else {
        header("Location: index.php?error=Failed to updated remark");
    }
}
if (isset($_GET['delete_remark'])) {
    $customer_id = $_GET['customer_id'];

    if ($admins->deleteRemark($customer_id)) {
        header("Location: index.php?success=Remark deleted successfully");
    } else {
        header("Location: index.php?error=Failed to delete remark");
    }
}
