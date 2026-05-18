<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/deliveries.php';

requireAuth();

$conn = Database::getConnection();

try {
    $courierId = $_SESSION['id'] ?? null;
    $deliveryId = (int)$_POST['consegna_id'] ?? null;

    if (!isset($courierId) || !isset($deliveryId)) {
        throw new Exception("Campi mancanti");
    }

    $conn->begin_transaction();

    if (!acceptDelivery($conn, $deliveryId, $courierId)) {
        $e = "Errore nell'accettazione della consegna #" . urlencode($deliveryId);
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
