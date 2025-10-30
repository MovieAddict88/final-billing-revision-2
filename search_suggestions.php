<?php
require_once 'includes/headx.php';
require_once 'includes/classes/admin-class.php';

$admins = new Admins($dbh);
header('Content-Type: application/json');

$type = isset($_GET['type']) ? $_GET['type'] : 'customers';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 8;

$suggestions = [];
if ($q === '') {
    echo json_encode($suggestions);
    exit;
}

try {
    switch ($type) {
        case 'products':
            $rows = $admins->fetchProductsPage(0, $limit, $q);
            if ($rows) {
                foreach ($rows as $r) {
                    $suggestions[] = [
                        'label' => $r->pro_name,
                        'value' => $r->pro_name,
                    ];
                }
            }
            break;
        case 'users':
            $rows = $admins->fetchAdminPage(0, $limit, $q);
            if ($rows) {
                foreach ($rows as $r) {
                    $suggestions[] = [
                        'label' => $r->full_name,
                        'value' => $r->full_name,
                    ];
                }
            }
            break;
        case 'customers':
        default:
            $rows = $admins->fetchCustomersPage(0, $limit, $q);
            if ($rows) {
                foreach ($rows as $r) {
                    $suggestions[] = [
                        'label' => $r->full_name,
                        'value' => $r->full_name,
                    ];
                }
            }
            break;
    }
} catch (Exception $e) {
    // ignore
}

echo json_encode($suggestions);
