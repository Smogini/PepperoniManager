<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/orders.php';

requireAuth();

$conn = Database::getConnection();
$orderStatus = Order_Status::map();

try {
    $orderId = (int)$_POST['ordine_id'];

    updateOrderStatus($conn, $orderId, $orderStatus['ACCETTATO'], $orderStatus['IN_ATTESA']);

    header("Location: ../pages/manage_orders.php");
    exit;
} catch(Exception $e) {

    header("Location: ../pages/manage_orders.php?error=" . urlencode($e->getMessage()));
    exit;
}