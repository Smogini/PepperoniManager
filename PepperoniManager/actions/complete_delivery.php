<?php
require_once '../config/database.php';
require_once '../config/auth.php';

requireAuth();

$conn = Database::getConnection();

try {
    $deliveryId = (int)$_POST['consegna_id'];
    $orderId = (int)$_POST['ordine_id'];

    $updateDelivery = "
        UPDATE consegna
        SET stato = 'CONSEGNATA'
        WHERE id = ?
        AND stato = 'IN_CONSEGNA'
    ";

    $stmt1 = $conn->prepare($updateDelivery);
    $stmt1->bind_param("i", $deliveryId);
    $stmt1->execute();

    $updateOrder = "
        UPDATE ordine
        SET stato = 'COMPLETATO'
        WHERE id = ?
    ";

    $stmt2 = $conn->prepare($updateOrder);
    $stmt2->bind_param("i", $orderId);
    $stmt2->execute();

    header("Location: ../pages/view_delivery.php");
    exit;
} catch(Exception $e) {
    header("Location: ../pages/view_delivery.php?error=" . urlencode($e->getMessage()));
    exit;
}