<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/orders.php';
require_once '../queries/deliveries.php';

requireAuth();

$conn = Database::getConnection();

$orderStatus = Order_Status::map();
$deliveryStatus = Delivery_Status::map();

try {
    $orderId = (int) $_POST['ordine_id'];

    if (!isset($orderId)) {
        throw new Exception("Id ordine mancante");
    }

    $conn->begin_transaction();

    if (!completeOrder($conn, $orderId)) {
        $e = "Errore nel completamento dell'ordine #" . urlencode($orderId);
        header("Location: ../pages/manage_orders.php?error=" . urlencode($e));
        exit;
    }

    if (!holdDelivery($conn, $orderId)) {
        $e = "Errore nella consegna con id ordine #" . urlencode($orderId);
        header("Location: ../pages/manage_orders.php?error=" . urlencode($e));
        exit;
    }

    $conn->commit();

    header("Location: ../pages/manage_orders.php");
    exit;
} catch(Exception $e) {
    $conn->rollback();
    header("Location: ../pages/manage_orders.php?error=" . urlencode($e->getMessage()));
    exit;
}