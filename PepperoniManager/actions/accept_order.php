<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/orders.php';

startSession();

$conn = Database::getConnection();

try {
    $orderId = (int) $_POST['ordine_id'] ?? null;

    if (!isset($orderId)) {
        throw new Exception("Id ordine mancante");
    }

    $conn->begin_transaction();

    if (!acceptOrder($conn, $orderId)) {
        $e = "Errore nell'accettazione dell'ordine #" . urlencode($orderId);
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
