<?php
session_start();
unset($_SESSION['customer_id']);
header('Location: customer_login.php');
exit();
?>