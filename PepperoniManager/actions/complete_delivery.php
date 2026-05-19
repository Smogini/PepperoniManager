<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/deliveries.php';
require_once '../queries/orders.php';

startSession();

$conn = Database::getConnection();

try {
    $deliveryId = (int) $_POST['consegna_id'] ?? null;
    $orderId = (int) $_POST['ordine_id'] ?? null;

    if (!isset($deliveryId) || !isset($orderId)) {
        throw new Exception("Campi mancanti");
    }

    $conn->begin_transaction();

    if (!completeDelivery($conn, $deliveryId)) {
        $e = "Errore nel completamento della consegna #" . urlencode($deliveryId);
        header("Location: ../pages/manage_deliveries.php?error=" . urlencode($e));
        exit;
    }

    $conn->commit();

    header("Location: ../pages/manage_deliveries.php");
    exit;
} catch(Exception $e) {
    $conn->rollback();
    header("Location: ../pages/manage_deliveries.php?error=" . urlencode($e->getMessage()));
    exit;
}
