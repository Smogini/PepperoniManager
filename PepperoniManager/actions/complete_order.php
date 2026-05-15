<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/orders.php';

requireAuth();

$conn = Database::getConnection();
$orderStatus = Order_Status::map();
$deliveryStatus = Delivery_Status::map();

try {

    $orderId = (int)$_POST['ordine_id'];

    updateOrderStatus(
        $conn,
        $orderId,
        $orderStatus['COMPLETATO'],
        $orderStatus['ACCETTATO']
    );

    $sql = "
        UPDATE consegna
        SET stato = ?
        WHERE ordine_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $deliveryStatus['IN_ATTESA'], $orderId);
    $stmt->execute();

    header("Location: ../pages/manage_orders.php");
    exit;
} catch(Exception $e) {
    header("Location: ../pages/manage_orders.php?error=" . urlencode($e->getMessage()));
    exit;
}